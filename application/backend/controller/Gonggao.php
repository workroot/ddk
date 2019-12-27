<?php
namespace app\backend\controller;

use app\backend\base\AuthController;
use app\backend\logic\GonggaoLogicQuery;
use app\common\base\Mapper;
use app\common\model\Gonggao as GonggaoModel;
header("content-type:text/html;charset=utf-8");         //设置编码
/**
 * 公告
 * Class AdminUser
 * @package app\admin\controller
 */
class Gonggao extends AuthController
{
    protected $gonggao_model;

    protected function _initialize()
    {
        parent::_initialize();
        $this->gonggao_model = new GonggaoModel();
		
    }

    /**
     * 公告管理
     * @param string $keyword
     * @param int    $page
     * @return mixed
     */
    public function index($keyword = '', $page = 1)
    {
        try{
            $data = GonggaoLogicQuery::getInstance()->indexList(['keyword'=>$keyword,'page'=>$page],$this->gonggao_model);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->fetch('index', ['gonggao_list' => $data, 'keyword' => $keyword]);
    }
	

	
    /**
     * 添加
     * @return mixed
     */
    public function add()
    {
        $announcement = Mapper::$ANNOUNCEMENT;
        $this->assign("announcement",$announcement);
		return $this->fetch();
    }

    /**
     * 保存
     * @return mixed|string
     */
    public function save()
    {
        try {
            if ($this->request->isPost()) {
                $data = $this->request->post();
                $status = GonggaoLogicQuery::getInstance()->save($data,$this->gonggao_model);
            }
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->renderSuccess($status,['redirect'=>'/backend/gonggao/index']);
    }

    /**
     * 编辑
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $gonggao = $this->gonggao_model->find($id);
        $announcement = Mapper::$ANNOUNCEMENT;
        $this->assign("announcement",$announcement);
        return $this->fetch('edit', ['gonggao' => $gonggao]);
    }

    /**
     * 更新口子
     * @param $id
     * @return mixed|string
     */
    public function update()
    {
        try {
            if ($this->request->isPost()) {
                $data = $this->request->post();
                $status = GonggaoLogicQuery::getInstance()->update($data,$this->gonggao_model);
            }
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->renderSuccess($status,['redirect'=>'/backend/gonggao/index']);
    }


    /**
     * 文章查询
     * @return mixed|string
     */
    public function title()
    {
        try{
            $id=input('id');
            $data = GonggaoLogicQuery::getInstance()->findOne(['id'=>$id],'id,title,marks');
            $data['content'] = htmlspecialchars_decode($data['marks']);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->renderSuccess($data);
    }

    /**
     * 删除口子
     * @param $id
     */
    public function delete($id)
    {
        if ($this->gonggao_model->destroy($id)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
	
}