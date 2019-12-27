<?php

namespace app\calculator\controller;

use app\calculator\base\AuthController;
use app\calculator\logic\AcontentLogicQuery;
use app\common\model\Clawyer as Clawyer;
use think\Db;

header("content-type:text/html;charset=utf-8");
class Acontent extends AuthController{
    protected $clawyer_model;
    public function _initialize(){
        parent::_initialize();
        $this->clawyer_model = new Clawyer();
    }



    public function index(){
        try{
            $params = $this->request->param();
            $data = AcontentLogicQuery::getInstance()->indexList($params,$this->clawyer_model);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        $this->assign('data',$data);
        return $this->fetch();
    }


    /**
     * 添加律师问题
     * @return string
     */
    public function add(){
        try {
            if ($this->request->isPost()) {
                $data = $this->request->post();
                $status = AcontentLogicQuery::getInstance()->save($data,$this->clawyer_model);
            }
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->renderSuccess($status,['redirect'=>'/calculator/acontent/index']);
    }


    /**
     *  获取单条数据
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function tai(){
        $id = $this->request->param('id');
        $data = Db::name('clawyer')->where('id','=',$id)->find();
        return $this->renderSuccess('',['data'=>$data]);
    }



    /**
     * 更新数据
     * @return mixed|string
     */
    public function update(){
        try{
            $result = AcontentLogicQuery::getInstance()->updated('',$this->clawyer_model);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->renderSuccess($result,['redirect'=>'/calculator/acontent/index']);
    }



    /**
     * 多条删除
     * @param $id
     */
    public function deletes($id){
        $ids = explode(',',$id);
        $this->clawyer_model->destroy($ids);
    }


    /**
     * 删除口子
     * @param $id
     */
    public function delete($id)
    {
        $this->clawyer_model->destroy($id);
    }
}