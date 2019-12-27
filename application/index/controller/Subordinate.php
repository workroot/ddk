<?php

namespace app\index\controller;

use app\common\base\AuthController;
use app\index\logic\SubordinateLogicQuery;

class Subordinate extends AuthController{



    public function index(){

        return $this->fetch();
    }


    public function query(){
        try{
            $data = SubordinateLogicQuery::getInstance()->querys();
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $data;
    }


}