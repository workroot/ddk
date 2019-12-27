<?php
namespace app\calculator\controller;


use app\calculator\base\AuthController;
use app\calculator\logic\ApayLogicQuery;
use app\common\model\LawyerOrder as Order;

class Apay extends AuthController{

    protected $lawyer_order_model;
    public function _initialize(){
        parent::_initialize();
        $this->lawyer_order_model = new Order();
    }


    /**
     * 列表
     * @return mixed|string
     */
    public function index(){
        try{
            $params = $this->request->param();
            $data = ApayLogicQuery::getInstance()->indexList($params,$this->lawyer_order_model);
            $count = ApayLogicQuery::getInstance()->count();
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        $this->assign('data',$data);
        $this->assign('count',$count);
        return $this->fetch();
    }



    /**
     * 多条删除
     * @param $id
     */
    public function deletes($id){
        $ids = explode(',',$id);
        $this->lawyer_order_model->destroy($ids);
    }


    /**
     * 删除口子
     * @param $id
     */
    public function delete($id)
    {
        $this->lawyer_order_model->destroy($id);
    }



}