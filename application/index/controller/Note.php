<?php

namespace app\index\controller;


use app\common\base\Controllers;
use app\index\logic\NoteLogicQuery;

class Note extends Controllers{


    public function index(){
        try{
            $data = NoteLogicQuery::getInstance()->detail();
            $this->assign('data',$data);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->fetch();
    }




}