<?php
namespace app\backend\controller;


use app\backend\base\AuthController;
use app\backend\logic\AgentLogicQuery;
use app\backend\logic\ProductLogicQuery;
use app\common\model\Product as ProductModel;

class Product extends AuthController{

    protected $product_model;

    protected function _initialize()
    {
        parent::_initialize();
        $this->product_model = new ProductModel();

    }


    /**
     * 代理类型类别
     * @param string $keyword
     * @param int $page
     * @return mixed|string
     */
    public function index($keyword = '', $page = 1){
        try{
            $list = ProductLogicQuery::getInstance()->productList(['keyword'=>$keyword,'page'=>$page],$this->product_model);
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
            $result = ProductLogicQuery::getInstance()->save('',$this->product_model);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->renderSuccess($result,['redirect'=>'/backend/product/index']);
    }


    /**
     * 编辑数据
     * @return mixed|string
     */
    public function edit(){
        try{
            $data = ProductLogicQuery::getInstance()->findOne('',$this->product_model);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        $this->assign('product',$data);
        return $this->fetch();
    }


    /**
     * 更新数据
     * @return mixed|string
     */
    public function update(){
        try{
            $result = ProductLogicQuery::getInstance()->updated('',$this->product_model);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->renderSuccess($result,['redirect'=>'/backend/product/index']);
    }


    /**
     * 删除
     * @param $id
     */
    public function delete($id){
        if ($this->product_model->destroy($id)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }







}