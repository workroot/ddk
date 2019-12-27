<?php

namespace app\backend\controller;

use app\backend\base\AuthController;
use app\backend\logic\AuthAgentLogicQuery;
use app\common\model\AuthAgent as AuthAgentModel;

class Authagent extends AuthController{

    protected $auth_agent_model;

    protected function _initialize()
    {
        parent::_initialize();
        $this->auth_agent_model = new AuthAgentModel();

    }


    /**
     * 代理类型类别
     * @param string $keyword
     * @param int $page
     * @return mixed|string
     */
    public function index($keyword = '', $page = 1){
        try{
            $list = AuthAgentLogicQuery::getInstance()->agentList(['keyword'=>$keyword,'page'=>$page],$this->auth_agent_model);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->fetch('index', ['list' => $list, 'keyword' => $keyword]);
    }


    /**
     * 添加页面
     * @return mixed
     */
    public function add(){
        $agent = db('agent')->field('id,agent_name')->select();
        $product = db('product')->field('id,name')->select();
        $this->assign('product_list',$product);
        $this->assign('agent_list',$agent);
        return $this->fetch();
    }


    /**
     * 提交
     * @return mixed|string
     */
    public function save(){
        try{
            $result = AuthAgentLogicQuery::getInstance()->save('',$this->auth_agent_model);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->renderSuccess($result,['redirect'=>'/backend/authagent/index']);
    }


    /**
     * 编辑数据
     * @return mixed|string
     */
    public function edit(){
        try{
            $agent = db('agent')->field('id,agent_name')->select();
            $product = db('product')->field('id,name')->select();
            $data = AuthAgentLogicQuery::getInstance()->findOne('',$this->auth_agent_model);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        $this->assign('product_list',$product);
        $this->assign('agent_list',$agent);
        $this->assign('agent',$data);
        return $this->fetch();
    }


    /**
     * 更新数据
     * @return mixed|string
     */
    public function update(){
        try{
            $result = AuthAgentLogicQuery::getInstance()->updated('',$this->auth_agent_model);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->renderSuccess($result,['redirect'=>'/backend/authagent/index']);
    }


    /**
     * 删除
     * @param $id
     */
    public function delete($id){
        if ($this->auth_agent_model->destroy($id)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
}