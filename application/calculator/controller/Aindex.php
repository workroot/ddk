<?php
namespace app\calculator\controller;

use app\calculator\base\AuthController;
use app\calculator\logic\AindexLogicQuery;

class Aindex extends AuthController{


    public function index(){
        try{
            $data = AindexLogicQuery::getInstance()->count();
        }catch(\Exception $e){
            return $this->html_404($this->renderError($e));
        }
        $this->assign('data',$data);
        return $this->fetch();
    }


}