<?php

namespace app\index\controller;


use app\common\base\Controllers;
use app\common\base\Mapper;
use app\index\logic\AgentPriceLogicQuery;
use app\index\logic\IndexLogicQuery;
use app\index\logic\OrderLogicQuery;
use app\index\logic\WxPayLogicQuery;
use helper\Rsas;
use think\Db;
use think\Exception;

class Wxpay extends Controllers {


     /**
     * 支付
     * @return mixed
     * @throws \Exception
     */
    public function pay(){
        $is_weixin = $this->isWeiXinBrowser();
        $get = $this->request->param();
        if(isset($get['sign'])){
            $data = Rsas::getInstance()->decode($get['sign']);
            parse_str($data,$params);
        }else{
            throw new Exception('违规操作',1);
        }
        if($is_weixin){
            $params['source'] = 1;
            $data = WxPayLogicQuery::getInstance()->order($params);
            $data['price'] = $data['price'] * 100;
            $result = WxPayLogicQuery::getInstance()->pay($data);
            $this->assign('order_no',$data['number_order']);
            $this->assign('result',$result);
            return $this->fetch();
        }else{
            $price = AgentPriceLogicQuery::getInstance()->findOne(['id'=>$params['pid']]);
            $this->assign('price',$price['price']);
            $this->assign('data',$params);
            return $this->fetch('hpay');
        }
    }

    /**
     * 支付宝支付
     * @throws \Exception
     */
    public function alypay(){
        try{
            $get = $this->request->param();
            $get['source'] = 2;
            $data = WxPayLogicQuery::getInstance()->order($get);
            $result = WxPayLogicQuery::getInstance()->alpay($data);
            $this->assign('price',$data['price']/100);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
    }


    /**
     * 支付宝回调
     * @return string
     */
    public function alnotify(){
        try{
            $get = $this->request->param();
            OrderLogicQuery::getInstance()->update($get);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
    }


    /**
     * 回调
     * @return string
     */
    public function notify(){
        try{
            WxPayLogicQuery::getInstance()->notify();
        }catch(\Exception $e){
            return $this->renderError($e);
        }
    }


    /**
     * 判断登录平台
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


}