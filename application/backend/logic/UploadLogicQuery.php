<?php

namespace app\backend\logic;

use app\backend\base\LogicQuery;
use app\common\base\Upload;

class UploadLogicQuery extends LogicQuery{


    /**
     * 上传
     * @param null $params
     * @return array|string
     * @throws \Exception
     */
    public function single($params = null){
        try{
            $result = Upload::submit($params);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $result;
    }
}