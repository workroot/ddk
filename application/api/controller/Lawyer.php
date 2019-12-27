<?php

namespace app\api\controller;

use app\api\logic\LawyerLogicQuery;
use app\common\base\Controllers;
use app\common\base\PublicMethod;

class Lawyer extends Controllers {


    /**
     * 律师咨询
     * @return mixed
     */
    public function add(){
        try{
            $data = LawyerLogicQuery::getInstance()->review();
        }catch(\Exception $e){
            return $this->html_404($this->renderError($e));
        }
        return $this->renderSuccess('',['redirect'=>'/index/lawyer/payment/pid/'.$data['pid'].'/price/'.$data['price'].'/lid/'.$data['lid']]);
    }



}