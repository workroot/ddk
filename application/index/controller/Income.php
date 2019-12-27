<?php

namespace app\index\controller;

use app\common\base\Controllers;

class Income extends Controllers{

    public function index(){
        return $this->fetch('index');
    }

}