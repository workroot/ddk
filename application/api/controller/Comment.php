<?php
namespace app\api\controller;

use app\api\logic\CommentLogicQuery;
use app\common\base\Controllers;

class Comment extends Controllers {

    /**
     * 列表数据
     * @return mixed|string
     */
    public function index(){
        try{
            $post = $this->request->param();
            $data = CommentLogicQuery::getInstance()->index($post);
        }catch(\Exception $e){
            if(empty($post)){
                return $this->html_404($this->renderError($e));
            }else{
                return $this->renderError($e);
            }
        }
        return $this->renderSuccess($data);
    }


    /**
     * 添加评论
     * @return mixed|string
     */
    public function save(){
        try{
            $post = $this->request->param();
            $status = CommentLogicQuery::getInstance()->save($post);
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