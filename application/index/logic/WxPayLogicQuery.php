<?php

namespace app\index\logic;


use app\common\base\LogicQuery;
use app\common\base\Mapper;
use app\common\base\PublicMethod;
use app\common\model\Order;
use helper\Assistant;
use helper\PublicHelper;
use think\Db;
use think\Exception;
use think\Session;
use wxpay\WxPayNotify;
use helper\Rsas;


class WxPayLogicQuery extends LogicQuery
{
    /**
     * 微信页面支付
     * @param null $params
     * @return array
     * @throws \Exception
     */
    public function pay($params = null)
    {
        try {
            $code = @$_GET["code"];
            $result = \wxpay\JsApiPay::getPayParams($params, isset($code) && !empty($code) ? $code : '');
        } catch (\Exception $e) {
            $this->log($e);
            throw $e;
        }
        return $result;
    }



    /**
     * 信息回调
     * @throws \Exception
     */
    public function notify()
    {
        try {
            $notify = new \wxpay\Notify();
            $notify->Handle();
        } catch (\Exception $e) {
            $this->log($e);
            throw $e;
        }
    }


    /**
     * 支付宝支付方式
     * @param null $params
     * @throws \Exception
     */
    public function alpay($params = null)
    {
    	$sign = Rsas::getInstance()->encode(http_build_query(['id'=>$params['cid'],'out_trade_no'=>$params['number_order']]));
        try {
            # 拉起支付宝
            $wap_config = [
                //合作身份者id，以2088开头的16位纯数字
                'partner' => '2088331380973764',
                //收款支付宝账号
                'seller_id' => '18062677701@163.com',
                // 商户的私钥（后缀是.pen）文件相对路径
                'private_key_path' => '/www/wwwroot/www.szdotcom.cn/extend/alipaywap/key/rsa_private_key.pem',
                //支付宝公钥（后缀是.pen）文件相对路径
                'ali_public_key_path' => '/www/wwwroot/www.szdotcom.cn/extend/alipaywap/key/alipay_public_key.pem',
                //签名方式
                'sign_type' => strtoupper('MD5'),
                'key' => 'ef19eg11mvccjaa0q0y4y36tbz5ysvdw',
                //字符编码格式 目前支持 gbk 或 utf-8
                'input_charset' => strtolower('utf-8'),
                //ca证书路径地址，用于curl中ssl校验
                //请保证cacert.pem文件在当前文件夹目录中
                'cacert' => getcwd() . '/Think/Library/Org/Alipaywap/cacert.pem',
                //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
                'transport' => 'http',
                //异步通知url base64_decode(base64_decode($params['cop']))
                'notify_url' => 'http://www.szdotcom.cn/index/wxpay/alnotify',
                //跳转通知url
                'return_url' => 'http://www.szdotcom.cn/index/index/detail?signs='.$sign,
            ];

            //构造要请求的参数数组
            $parameter = array(
                "service" => "alipay.wap.create.direct.pay.by.user",
                "partner" => $wap_config['partner'],
                "_input_charset" => strtolower($wap_config['input_charset']),
                "sign_type" => $wap_config['sign_type'],
                "notify_url" => $wap_config['notify_url'],
                "return_url" => isset($params['return_url']) ? $params['return_url'] : $wap_config['return_url'],
                "out_trade_no" => $params['number_order'],//商户订单号
                "subject" => '机构分析查询',//订单名称
                "total_fee" => $params['price'],//付款金额
                "seller_id" => $wap_config['seller_id'],
                "payment_type" => "1", //支付类型，不能修改
                "body" => '大数据查询分析优化',//订单描述
                "show_url" => '',//商品展示地址
                "it_b_pay" => '1h',//设置超时时间为1小时
            );
            if (isset($params['app_pay']) && $params['app_pay'] == 'Y') {
                $parameter['app_pay'] = 'Y'; //是否调起支付宝客户端进行支付
            }
            //建立请求
            $alipaySubmit = new \alipaywap\AlipaySubmit($wap_config);
            $html_text = $alipaySubmit->buildRequestForm($parameter, "post", '');
            echo $html_text;
        } catch (\Exception $e) {
            $this->log($e);
            throw $e;
        }
    }


    /**
     * 订单插入
     * @param null $params
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \Exception
     */
    public function order($params = null)
    {
        self::startTrans();
        try {
            if (empty($params)) {
                throw new Exception('参数错误', 1);
            }

            if(empty($params['pid'])){
                throw new Exception('参数错误',1);
            }
            //用户UID
            $uid = session('uid');
            $price = AgentPriceLogicQuery::getInstance()->findOne(['id'=>$params['pid']]);
            $authAgent =  Db('AuthAgent')->where('id','=',$price['a_p_id'])->find();

            if(isset($params['cid']) && !empty($params['cid'])){
                $company = IndexLogicQuery::getInstance()->findOne(['id'=>$params['cid']]);
            }
            if(isset($params['lid']) && !empty($params['lid'])){
                $company = LawyerLogicQuery::getInstance()->findOne(['id'=>$params['lid']]);
            }
            $data = [
                'uid' => isset($uid)?$uid:0, //用户UID
                'cid' => isset($company) && !empty($company) ? $company['id'] : 0 , //商品ID
                'pid' => isset($price) && !empty($price) ? $price['id'] : 0 , //推广连接ID
                'cname' => isset($company['name']) && !empty($company['name']) ? $company['name'] : $price['rename'],
                'p_type' => $price['product_type'],
                'p_name' => $price['rename'],
                'proxyid' => isset($price['uid']) ? $price['uid'] : 0, //代理UID
                'number_order' => PublicHelper::number(),
                'body' => Mapper::$WX_PRODUCE_NAME[$price['product_type']],
                'price' => $price['price'],
                'commission' => $authAgent['erjiprice'], //二级提成
                'source' => $params['source'],//支付类型
                'status' => 0,
                'isstop' => 1,
                'createdAt' => time(),
            ];
            $order = new Order();
            $order->allowField(true)->save($data);
            self::commit();
        } catch (\Exception $e) {
            $this->log($e);
            self::rollback();
            throw $e;
        }
        return $data;
    }


}