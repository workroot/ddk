<?php

namespace app\index\controller;


use app\common\base\Controllers;
use app\index\logic\NoteLogicQuery;

class Announcement extends Controllers {


    /**
     * 新闻列表
     * @return mixed|string
     */
    public function index(){
        try{
            $data = NoteLogicQuery::getInstance()->query();
            $this->assign('data',$data);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->fetch('index');
    }

}