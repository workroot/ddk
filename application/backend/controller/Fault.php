<?php

namespace app\backend\controller;


use app\backend\base\AuthController;
use app\backend\logic\FaultLogicQuery;
use app\common\model\Fault as Fault_model;

class Fault extends AuthController{

    protected $fault_model;

    protected function _initialize()
    {
        parent::_initialize();
        $this->fault_model = new Fault_model();
    }

    /**
     * 过失列表
     * @return mixed|string
     */
    public function index(){
        try{
            $get = $this->request->param();
            $data = FaultLogicQuery::getInstance()->query();
            $this->assign('get',$get);
            $this->assign('data',$data);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->fetch();
    }


    /**
     * 过失数据删除
     * @param string $id
     */
    public function del(){
        $get = $this->request->param();
        if ($this->fault_model->destroy($get['id'])) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

}