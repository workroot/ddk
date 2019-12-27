<?php
namespace app\backend\controller;


use app\backend\base\AuthController;
use app\backend\logic\AgentLogicQuery;
use app\common\model\Agent as AgentModel;

class Agent extends AuthController{

    protected $agent_model;

    protected function _initialize()
    {
        parent::_initialize();
        $this->agent_model = new AgentModel();

    }


    /**
     * 代理类型类别
     * @param string $keyword
     * @param int $page
     * @return mixed|string
     */
    public function index($keyword = '', $page = 1){
        try{
            $list = AgentLogicQuery::getInstance()->agentList(['keyword'=>$keyword,'page'=>$page],$this->agent_model);
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
        return $this->fetch();
    }


    /**
     * 提交
     * @return mixed|string
     */
    public function save(){
        try{
            $result = AgentLogicQuery::getInstance()->save('',$this->agent_model);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->renderSuccess($result,['redirect'=>'/backend/agent/index']);
    }


    /**
     * 编辑数据
     * @return mixed|string
     */
    public function edit(){
        try{
            $data = AgentLogicQuery::getInstance()->findOne('',$this->agent_model);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        $this->assign('agent',$data);
        return $this->fetch();
    }


    /**
     * 更新数据
     * @return mixed|string
     */
    public function update(){
        try{
            $result = AgentLogicQuery::getInstance()->updated('',$this->agent_model);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->renderSuccess($result,['redirect'=>'/backend/agent/index']);
    }


    /**
     * 删除
     * @param $id
     */
    public function delete($id){
        if ($this->agent_model->destroy($id)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }







}