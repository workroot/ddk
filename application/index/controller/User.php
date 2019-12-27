<?php

namespace app\index\controller;


use app\common\base\AuthController;

class User extends AuthController{

    public function index(){
        return $this->fetch();
    }
}