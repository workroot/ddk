<?php

namespace app\index\controller;

use app\common\base\Controllers;

class Client extends Controllers{

    public function login(){
        return $this->fetch();
    }



}