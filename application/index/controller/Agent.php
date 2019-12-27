<?php
namespace app\index\controller;
use app\common\base\Controllers;

class Agent extends Controllers {



    public function index(){
        return $this->fetch('income');
    }

    public function ranking(){
        return $this->fetch('ranking');
    }

    public function extension(){
        return $this->fetch('extension');
    }

    public function propaganda(){
        return $this->fetch('propaganda');
    }

    public function agent(){
        return $this->fetch('user_agent');
    }

    public function handbook(){
        return $this->fetch('handbook');
    }

    public function userinfo(){
        return $this->fetch('user_info');
    }

    public function withdrawal(){
        return $this->fetch('withdrawal');
    }

    public function setPrice(){
        return $this->fetch('set_price');
    }

    public function lawyerPrice(){
        return $this->fetch('lawyer_price');
    }

    public function spreadPrice(){
        return $this->fetch('spread_price');
    }

    public function threePrice(){
        return $this->fetch('three_price');
    }

    public function allPrice(){
        return $this->fetch('all_price');
    }

    public function promotion(){
        return $this->fetch('promotion');
    }

    public function message(){
        return $this->fetch('message');
    }

    public function bank(){
        return $this->fetch('bank');
    }

    public function addBank(){
        return $this->fetch('add_bank');
    }

    public function subordinate(){
        return $this->fetch('subordinate');
    }

    public function article(){
        return $this->fetch('article');
    }

    public function commonProblem(){
        return $this->fetch('common_problem');
    }

    public function contact(){
        return $this->fetch('contact');
    }

    public function changePassword(){
        return $this->fetch('change_password');
    }

}