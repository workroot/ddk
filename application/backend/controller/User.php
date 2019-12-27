<?php

namespace app\backend\controller;

use app\backend\base\AuthController;
use app\index\logic\UserLogicQuery;
use app\common\model\User as UserModel;

class User extends AuthController{


    /**
     * 客户信息列表
     * @return mixed|string
     */
    public function index(){
        try{
            $params = $this->request->param();
            $data = UserLogicQuery::getInstance()->query();
            $this->assign('params',$params);
            $this->assign('data',$data['data']);
            $this->assign('statistics',$data['statistics']);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->fetch();
    }


    /**
     * excel 客户数据导出
     * @return string
     */
    public function excel(){
        try{
            $data = UserLogicQuery::getInstance()->excel();
        }catch(\Exception $e){
            return $this->renderError($e);
        }
    }



}