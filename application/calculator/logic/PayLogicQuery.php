<?php

namespace app\calculator\logic;

use app\calculator\base\LogicQuery;
use app\calculator\lawyerpays\JsapiPay;

class PayLogicQuery extends LogicQuery{




    public function pay($params=null){
        try{
            if(empty($params)){
                $params = $this->request->param();
            }


        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
    }
}