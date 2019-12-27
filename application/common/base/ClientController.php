<?php

namespace app\common\base;


class ClientController extends Controllers{

    public function _initialize()
    {
        parent::_initialize();
        $uid = session('uid');
        if(!isset($uid) || empty($uid)){
            $uid = "";
        }
        if($uid == null || $uid == "" || $uid == "null" || $uid == 0 || $uid == false){
            return $this->redirect('/index/client/login', 1);
        }
    }

}
