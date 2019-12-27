<?php

namespace app\common\base;


class AuthController extends Controllers{

    public function _initialize()
    {
        parent::_initialize();
        $uid = session('user_id');
        if(!isset($uid) || empty($uid)){
            $uid = "";
        }
        if($uid == null || $uid == "" || $uid == "null" || $uid == 0 || $uid == false){
            return $this->redirect('/index/login/login', 1);
        }
    }

}
