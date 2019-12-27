<?php

namespace app\backend\controller;

use app\backend\base\AuthController;
use app\backend\logic\InquireLogicQuery;

class Inquire extends AuthController{


    /**
     * 查询客户列表
     * @return mixed|string
     */
    public function index(){
        try{
            $params = $this->request->param();
            $data = InquireLogicQuery::getInstance()->query();
            $this->assign('params',$params);
            $this->assign('data',$data['data']);
            $this->assign('statistics',$data['statistics']);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->fetch();
    }


    /**
     * excel 查询客户数据导出
     * @return string
     */
    public function excel(){
        try{
            $data = InquireLogicQuery::getInstance()->excel();
        }catch(\Exception $e){
            return $this->renderError($e);
        }
    }


}
