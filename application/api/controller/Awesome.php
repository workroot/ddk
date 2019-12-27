<?php

namespace app\api\controller;

use app\api\logic\AwesomeLogicQuery;
use app\common\base\Controllers;

class Awesome extends Controllers {

    /**
     * 点赞
     * @return mixed|string
     */
    public function save(){
        try{
            $post = $this->request->param();
            $status = AwesomeLogicQuery::getInstance()->save($post);
        }catch(\Exception $e){
            return $this->html_404($this->renderError($e));
        }
        return $this->renderSuccess($status);
    }


}