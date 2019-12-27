<?php
namespace app\backend\controller;

use app\backend\base\AuthController;
use app\backend\logic\NotetypeLogicQuery;
use app\common\model\Notetype as NotetypeModel;
header("content-type:text/html;charset=utf-8");         //设置编码
/**
 * 口子管理
 * Class AdminUser
 * @package app\admin\controller
 */
class Notetype extends AuthController
{
    protected $notetype_model;

    protected function _initialize()
    {
        parent::_initialize();
        $this->notetype_model = new NotetypeModel();
		
    }


    /**
     * 口子管理
     * @param string $keyword
     * @param int    $page
     * @return mixed
     */
    public function index($keyword = '', $page = 1)
    {
        try{
            $data = NotetypeLogicQuery::getInstance()->indexList(['keyword'=>$keyword,'page'=>$page],$this->notetype_model);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->fetch('index', ['notetype_list' => $data, 'keyword' => $keyword]);
    }
	

	
    /**
     * 添加口子
     * @return mixed
     */
    public function add()
    {
		return $this->fetch();
    }


    /**
     * 保存口子
     */
    public function save()
    {
       if ($this->request->isPost()) {
           try{
               $data = $this->request->post();
               $status = NotetypeLogicQuery::getInstance()->save($data,$this->notetype_model);
           }catch(\Exception $e){
               return $this->renderError($e);
           }
           return $this->renderSuccess($status,['redirect'=>'/backend/notetype/index']);
        }
    }

    /**
     * 编辑口子
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $notetype = $this->notetype_model->find($id);
        return $this->fetch('edit', ['notetype' => $notetype]);
    }

    /**
     * 更新
     * @return mixed|string
     */
    public function update()
    {
        if ($this->request->isPost()) {
            try{
                $data = $this->request->post();
                $status = NotetypeLogicQuery::getInstance()->update($data,$this->notetype_model);
            }catch(\Exception $e){
                return $this->renderError($e);
            }
            return $this->renderSuccess($status,['redirect'=>'/backend/notetype/index']);
        }
    }

    /**
     * 删除口子
     * @param $id
     */
    public function delete($id)
    {
        if ($this->notetype_model->destroy($id)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
	
}