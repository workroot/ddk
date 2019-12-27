<?php

namespace app\backend\controller;

use app\backend\base\AuthController;
use app\backend\logic\PlatformLogicQuery;

class Platform extends AuthController{



    /**
     * 平详情
     * @return mixed
     */
    public function index(){
        try{
            $data = PlatformLogicQuery::getInstance()->detail();
            $this->assign('img',$data['b_img']['thumb']);
            $this->assign('data',$data['data']);
            $this->assign('comment',$data['comment']);
            $this->assign('count',count($data['comment']));
        }catch(\Exception $e){
            $this->renderError($e);
        }
        return $this->fetch();
    }



    /**
     * 添加评论
     * @return mixed|string
     */
    public function save(){
        try{
            $post = $this->request->param();
            $status = PlatformLogicQuery::getInstance()->save($post);
        }catch(\Exception $e){
            if(empty($post)){
                return $this->html_404($this->renderError($e));
            }else{
                return $this->renderError($e);
            }
        }
        return $this->renderSuccess($status);
    }


    /**
     * 添加回复
     * @return mixed|string
     */
    public function reply(){
        try{
            $post = $this->request->param();
            $status = PlatformLogicQuery::getInstance()->reply($post);
        }catch(\Exception $e){
            if(empty($post)){
                return $this->html_404($this->renderError($e));
            }else{
                return $this->renderError($e);
            }
        }
        return $this->renderSuccess($status);
    }


}