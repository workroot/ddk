<?php
namespace app\api\controller;

use app\api\logic\ReplyLogicQuery;
use app\common\base\Controllers;

class Reply extends Controllers{

    /**
     * 添加评论
     * @return mixed|string
     */
    public function save(){
        try{
            $post = $this->request->param();
            $status = ReplyLogicQuery::getInstance()->save($post);
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