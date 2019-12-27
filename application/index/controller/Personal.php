<?php

namespace app\index\controller;


use app\common\base\AuthController;
use app\index\logic\PersonalLogicQuery;
use app\index\logic\ProvinceLogicQuery;

class Personal extends AuthController {


    /**
     * 个人中心
     * @return mixed
     */
    public function index(){
        try{
            $data = PersonalLogicQuery::getInstance()->getUser();
            $this->assign('data',$data);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->fetch('index');
    }


    /**
     * 更新密码页面
     * @return mixed
     */
    public function modify(){
        return $this->fetch('modifypass');
    }


    /**
     * 编辑个人信息
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function supplement(){

        try{
            $data = PersonalLogicQuery::getInstance()->getUser();
            //$user = db('user')->where('id','=',session('user_id'))->find();
            $this->assign('data',$data);
        }catch(\Exception $e){
            return $this->renderError($e);
        }



        return $this->fetch();
    }


    public function province(){
        try{
            $province = ProvinceLogicQuery::getInstance()->province();
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $province;
    }


    /**
     * 更新用户数据
     * @return mixed|string
     */
    public function edit(){
        try{
            $data = PersonalLogicQuery::getInstance()->edit();
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->renderSuccess($data);
    }










}