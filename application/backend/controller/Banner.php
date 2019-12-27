<?php
namespace app\backend\controller;

use app\backend\base\AuthController;
use app\backend\logic\BannerLogicQuery;
use app\common\base\Mapper;
use app\common\model\Banner as BannerModel;
header("content-type:text/html;charset=utf-8");         //设置编码
/**
 * 轮播图
 * Class AdminUser
 * @package app\admin\controller
 */
class Banner extends AuthController
{
    protected $banner_model;

    protected function _initialize()
    {
        parent::_initialize();
        $this->banner_model = new BannerModel();
		
    }

    /**
     * 轮播图管理
     * @param string $keyword
     * @param int    $page
     * @return mixed
     */
    public function index($keyword = '', $page = 1)
    {
        try{
            $data = BannerLogicQuery::getInstance()->indexList(['keyword'=>$keyword,'page'=>$page],$this->banner_model);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->fetch('index', ['banner_list' => $data, 'keyword' => $keyword]);
    }
	

	
    /**
     * 添加
     * @return mixed
     */
    public function add()
    {
        $carouset = Mapper::$CAROUSEL_TYPE;
        $this->assign('carouset',$carouset);
		return $this->fetch();
    }

    /**
     * 保存
     */
    public function save()
    {
        try{
            if ($this->request->isPost()) {
                $data            = $this->request->post();
                $status = BannerLogicQuery::getInstance()->save($data, $this->banner_model);
            }
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->renderSuccess($status,['redirect'=>'/backend/banner/index']);
    }

    /**
     * 编辑
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $banner = $this->banner_model->find($id);
        $carouset = Mapper::$CAROUSEL_TYPE;
        $this->assign('carouset',$carouset);
        return $this->fetch('edit', ['banner' => $banner]);
    }


    /**
     * 更新
     * @param $id
     * @return mixed|string
     */
    public function update($id)
    {
        try{
            if ($this->request->isPost()) {
                $data            = $this->request->post();
                $status = BannerLogicQuery::getInstance()->update($data, $this->banner_model);
            }
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->renderSuccess($status,['redirect'=>'/backend/banner/index']);
    }

    /**
     * 删除口子
     * @param $id
     */
    public function delete($id)
    {
        if ($this->banner_model->destroy($id)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
	
}