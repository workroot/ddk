<?php

namespace app\backend\controller;


use app\backend\base\AuthController;
use app\backend\logic\UploadLogicQuery;

class Uploads extends AuthController{


    /**
     * 上传
     * @return mixed|string
     */
    public function single(){
        try{
            if($this->request->isPost()){
                $data = $this->request->post();
                $status = UploadLogicQuery::getInstance()->single($data);
            }
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->renderSimpleJson($status);
    }
}