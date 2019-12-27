<?php

namespace app\calculator\controller;

use app\calculator\base\Controllers;
use app\calculator\lawyerpays\JsapiPay;
use app\calculator\logic\PayLogicQuery;
use app\calculator\lawyerpays\Notify;
use think\Db;

class Pay extends Controllers {
	
	
	
      public function pay(){
          $params = $this->request->param();
          $uid = session('uid');
          $uid = isset($uid) ? $uid : 0;
          $is_weixin = $this->isWeiXinBrowser();
          $order_no = mt_rand().time();
          $price = $params['price'];
          if(!isset($fpid) && empty($fpid)){
              $fpid = '759';
          }
          $baseuid = base64_encode(base64_encode($uid));
          $this->assign('uid',$baseuid);
          $ordernumber = "D".$uid.time();
          $data = [
              'body' => $ordernumber,
              'out_trade_no' => $order_no,
              'total_fee' =>$price * 100, //$price*100
              'createAt' => time(),
              'uid' => $uid,
              'cid' => $params['id'],
              'agentId' => $fpid,
          ];
          $orderid = db('LawyerOrder')->insertGetId($data);
          if ($is_weixin){
              //return $this->fetch('tishi');
              $code=@$_GET["code"];
              if ($code){
                  $result = JsapiPay::getPayParams($data,$code);
                  $this->assign("order_no",$order_no);
                  $this->assign("result",$result);
                  $this->assign("price",$price);
                  return $this->fetch();
              }else{
                  $result = JsapiPay::getPayParams($data);
              }
          }else{
              $this->assign("order_no",$order_no);
              $this->assign("price",$price);
              $this->assign("cid",$params['id']);
              return $this->fetch('hpay');
          }
      }



    /**
     * 微信回调
     */
    public function notify(){
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        if(!$xml){
            $xml = file_get_contents("php://input");
        }
        file_put_contents("88.txt",$xml);
       
        $notify = new Notify();
        
        $result = $notify->Handle();
    }


    /**
     * 支付状态
     * @return int|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function order_query(){
        $out_trade_no=input('order_no');
        $result = Db::name("LawyerOrder")->where(array("out_trade_no"=>$out_trade_no))->find();
        if ($result["status"]==1){
            return base64_encode(base64_encode($result['cid']));
            exit();
        }else{
            return 0;
            exit();
        }
    }


    /**
     * 答案
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function problem(){
        $cid = $this->request->param('cid');
        $data = Db::name("clawyer")->where(array("id"=>base64_decode(base64_decode($cid))))->find();
        $this->assign("data",$data);
        return $this->fetch();
    }


    /**
     * 判断是微信还是支付宝支付
     * @return bool
     */
    public function isWeiXinBrowser()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($user_agent, 'MicroMessenger') === false) {
            return false;
        } else {
            return true;
        }
    }


    /**
     * 支付宝支付
     */
    public function zfpay(){
        $params = $this->request->param();
        $wap_config = array(
            //合作身份者id，以2088开头的16位纯数字
            'partner' => '2088331380973764',
            //收款支付宝账号
            'seller_id' => '18062677701@163.com',
            // 商户的私钥（后缀是.pen）文件相对路径
            'private_key_path' => '/www/wwwroot/www_zsxycx_com/extend/alipaywap/key/rsa_private_key.pem',
            //支付宝公钥（后缀是.pen）文件相对路径
            'ali_public_key_path' => '/www/wwwroot/www_zsxycx_com/extend/alipaywap/key/alipay_public_key.pem',
            //签名方式
            'sign_type' => strtoupper('MD5'),
            'key' => 'ef19eg11mvccjaa0q0y4y36tbz5ysvdw',
            //字符编码格式 目前支持 gbk 或 utf-8
            'input_charset' => strtolower('utf-8'),
            //ca证书路径地址，用于curl中ssl校验
            //请保证cacert.pem文件在当前文件夹目录中
            'cacert' => getcwd().'/Think/Library/Org/Alipaywap/cacert.pem',
            //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
            'transport' => 'https',
            //异步通知url
            'notify_url' => 'https://www.zsxycx.com/calculator/pay/zfnotify',
            //跳转通知url
            'return_url' => 'https://www.zsxycx.com/calculator/pay/zfproblem'
        );

        //构造要请求的参数数组
        $parameter = array(
            "service"           => "alipay.wap.create.direct.pay.by.user",
            "partner"           => $wap_config['partner'],
            "_input_charset"    => strtolower($wap_config['input_charset']),
            "sign_type"         => $wap_config['sign_type'],
            "notify_url"        => $wap_config['notify_url'],
            "return_url"        => isset($params['return_url']) ? $params['return_url'] : $wap_config['return_url'],
            "out_trade_no"      => $params['order_no'],//商户订单号
            "subject"           => '律法查询',//订单名称
            "total_fee"         => $params['price'],//付款金额
            "seller_id"         => $wap_config['seller_id'],
            "payment_type"      => "1", //支付类型，不能修改
            "body"              => '律法查询',//订单描述
            "show_url"          => '',//商品展示地址
            "it_b_pay"          => '1h',//设置超时时间为1小时
        );
        if (isset($params['app_pay']) && $params['app_pay'] == 'Y') {
            $parameter['app_pay'] = 'Y'; //是否调起支付宝客户端进行支付
        }

        //建立请求
        $alipaySubmit = new \alipaywap\AlipaySubmit($wap_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"post",'');
        echo $html_text;
    }


    /**
     * 支付宝回调
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function zfnotify(){
            //验证成功
            $out_trade_no   = $_POST['out_trade_no'];      //商户订单号
            $trade_status   = $_POST['trade_status'];      //交易状态
            $total_fee      = $_POST['total_fee'];         //交易金额
            file_put_contents("50.txt",$trade_status);
            file_put_contents("60.txt",$out_trade_no);
            if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
                 $result = db('LawyerOrder')->where('out_trade_no','=', $out_trade_no)->update(['status'=>1]);
                 $product = Db::name("LawyerOrder")->where(array("out_trade_no"=>$out_trade_no))->where('status','=',1)->find();
                	Db::name('clawyer')->where('id','=',$product['cid'])->setInc('awesome');
                if($result){
                    exit('success');
                }
            }
        	exit('fail');
    }


    /**
     * 答案
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function zfproblem(){
        $out_trade_no = $this->request->param('out_trade_no');
        $product = Db::name("LawyerOrder")->where(array("out_trade_no"=>$out_trade_no))->where('status','=',1)->find();
        if($product){
        	 $data = Db::name("clawyer")->where(array("id"=>$product['cid']))->find();
        	 $this->assign("data",$data);
        	 return $this->fetch('problem');
        }
       
    }
}