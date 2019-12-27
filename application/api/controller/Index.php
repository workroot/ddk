<?php

namespace app\api\controller;


use app\api\logic\IndexLogicQuery;
use app\common\base\AuthController;
use app\common\base\Controllers;

class Index extends Controllers {


    /**
     * 查询记录
     * @return mixed|string
     */
    public function index(){
        try{
            $post = $this->request->param();
            $data = IndexLogicQuery::getInstance()->index($post,'c.id,c.name,c.logo');
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->renderSuccess('',['data'=>$data]);
    }


}