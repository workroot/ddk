<?php

namespace app\calculator\controller;

use app\calculator\base\AuthController;
use app\calculator\logic\AcommentLogicQuery;

use app\common\model\Comment as Comment;
use think\Db;


class Acomment extends AuthController{


    protected $comment_model;
    public function _initialize(){
        parent::_initialize();
        $this->comment_model = new Comment();
    }

    /**
     * 首页
     * @return mixed|string
     */
    public function index(){
        try{
            $params = $this->request->param();
            $data = AcommentLogicQuery::getInstance()->indexList($params,$this->comment_model);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        $this->assign('data',$data);
        return $this->fetch();
    }



}