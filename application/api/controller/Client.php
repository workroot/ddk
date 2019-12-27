<?php

namespace app\api\controller;


use app\api\logic\ClientLoginLogicQuery;
use app\common\base\Controllers;

class Client extends Controllers{
    /**
     * 客户登录
     * @return mixed|string
     */
    public function login(){
        try{
            $post = $this->request->param();
            $status = ClientLoginLogicQuery::getInstance()->login($post);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->renderSuccess($status,['redirect'=>'/index/platform/index','type'=>'1']);
    }


}