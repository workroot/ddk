<?php

namespace app\index\logic;


use app\common\base\LogicQuery;
use app\common\base\Mapper;
use app\common\base\DigitalHelper;
use app\common\model\AgentPrice;
use helper\Rsas;
use think\Db;
use think\Exception;

class PromotionLogicQuery extends LogicQuery{


    /**
     * 添加支付连接
     * @param null $params
     * @return int|string
     * @throws \Exception
     */
        public function price($params = null){
            try{
                if(empty($params)){
                    $params = $this->request->param();
                }
                $user = Db::name('User')->field('id,names,agent_class,mobile')->where('id',$params['uid'])->find();
                if(empty($user)){
                    throw new Exception('参数错误',0);
                }

                //判断用户是否添加代理
                $authAgent = Db::name('authAgent')->where(['aid'=>$user['agent_class'],'pid'=>$params['type']])->find();
                if(empty($authAgent)){
                    throw new Exception('请联系客服添加代理',0);
                }

                if(!isset($params['price']) && empty($params['price'])){
                    throw new Exception('金额不能为空',0);
                }

                if(!is_numeric($params['price'])){
                    throw new Exception('金额只能为数字',0);
                }

                if(!in_array($user['id'],Mapper::$TEST_ID) && $params['price'] < $authAgent['price']){
                    throw new Exception('金额不能低于成本价格',0);
                }

                if($params['price'] > $authAgent['highestprice']){
                    throw new Exception('金额不能高于平台设置价格',0);
                }
                $data = [
                    'a_p_id' => $authAgent['id'],
                    'product_type' => $params['type'],
                    'rename' => $params['rename'],
                    'price' => $params['price'],
                    'ticheng' => DigitalHelper::sub($params['price'],$authAgent['price'],2),
                    'uid' => $params['uid'],
                    'link_name' => $params['link_name'],
                    //'url' => $url,
                    'define' => 1,
                    'isdel' => 1,
                    'createdAt' => time(),
                ];
                $id = Db::name('AgentPrice')->insertGetId($data);
                if($id){
                    if(isset($params['type']) && $params['type'] == Mapper::PRODUCT_TYPE_TWO){
                        $url = '';
                    }else{
                        $host = 'http://'.$_SERVER['SERVER_NAME'];
                        $sign = Rsas::getInstance()->encode(http_build_query(['pid'=> $id,'uid'=>$user['id']]));
                        $url = $this->shortenSinaUrl($host.'/index/index/index?sign='.$sign);
                        Db::name('AgentPrice')->where('id',intval($id))->update(['url'=>$url]);
                    }
                }
            }catch(\Exception $e){
                $this->log($e);
                throw $e;
            }
            return $id;
        }


    /**
     *  获取链接判断数据
     * @param null $params
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \Exception
     */
        public function version_data($params = null){
            try{
                if(empty($params)){
                    $params = $this->request->param();
                }
                $uid = session('user_id');

                $user = Db::name('User')->field('id,names,agent_class,mobile')->where('id',Session('user_id'))->find();
                //判断用户是否添加代理
                $authAgent = Db::name('authAgent')->where(['aid'=>$user['agent_class'],'pid'=>Mapper::PRODUCT_TYPE])->find();
                //查询版本
                $product = Db::name('product')->where(['id'=>$authAgent['pid']])->find();

                $user['price'] = $authAgent['price'];
                $user['pid'] = $authAgent['pid'];
                $user['pname'] = $product['name'];
                $user['prices'] = $product['prices'];
                return $user;

            }catch(\Exception $e){
                $this->log($e);
                throw $e;
            }

        }


    /**
     * 更新链接
     * @param null $params
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
        public function update($params = null){
            if(empty($params)){
                $params = $this->request->param();
            }

            if(!isset($params['id']) && empty($params['id'])){
                throw new Exception('参数错误',0);
            }

            $user = Db::name('User')->field('id,names,agent_class,mobile')->where('id',$params['uid'])->find();
            if(empty($user)){
                throw new Exception('参数错误',0);
            }

            //判断用户是否添加代理
            $authAgent = Db::name('authAgent')->where(['aid'=>$user['agent_class'],'pid'=>$params['type']])->find();
            if(empty($authAgent)){
                throw new Exception('请联系客服添加代理',0);
            }

            if(!isset($params['price']) && empty($params['price'])){
                throw new Exception('金额不能为空',0);
            }

            if(!is_numeric($params['price'])){
                throw new Exception('金额只能为数字',0);
            }

            if(!in_array($user['id'],Mapper::$TEST_ID) && $params['price'] < $authAgent['price']){
                throw new Exception('金额不能低于成本价格',0);
            }

            if($params['price'] > $authAgent['highestprice']){
                throw new Exception('金额不能高于平台设置价格',0);
            }

            $data = [
                'price' => trim($params['price']),
                'link_name' => trim($params['link_name']),
                'ticheng' => DigitalHelper::sub($params['price'],$authAgent['price'],2),
                'updatedAt' => time(),
            ];
           Db::name('AgentPrice')->where('id',intval($params['id']))->update($data);
        }


    /**
     * 删除数据
     * @param null $params
     * @throws \Exception
     */
        public function del($params = null){
            try{
                if(empty($params)){
                    $params = $this->request->param();
                }

                if(!isset($params['id']) && empty($params['id'])){
                    throw new Exception('参数错误',0);
                }
                $del = Db::name('AgentPrice')->where('id',intval($params['id']))->delete();
                if(!$del){
                    throw new Exception('删除失败',0);
                }
            }catch(\Exception $e){
                $this->log($e);
                throw $e;
            }
        }


    /**
     * [shortenSinaUrl 短网址接口]
     * @param  [integer] $long_url   需要转换的网址
     * @return [string]          [返回转结果]
     * @author king
     */
    public function shortenSinaUrl($long_url)
    {
        //apikey需要自己申请  方法见网址：   http://c7.gg/page/apidoc.html
        $apiUrl = 'http://api.c7.gg/api.php?format=json&url=' . $long_url . "&apikey=oJmWtN079SVfXf9iFk@ddd";
        $curlObj = curl_init();
        curl_setopt($curlObj, CURLOPT_URL, $apiUrl);
        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlObj, CURLOPT_HEADER, 0);
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, array(
            'Content-type:application/json'
        ));
        $response = curl_exec($curlObj);
        curl_close($curlObj);
        $json = json_decode($response);
        if (empty($json->error)) {
            $url = $json->url;
        } else {
            $url = "none";
        }
        return $url;
    }
}