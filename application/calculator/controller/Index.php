<?php

namespace app\calculator\controller;


use app\calculator\base\Controllers;
use think\Db;

class Index extends Controllers{


    /**
     * 计算台
     * @return mixed|string
     */
      public function index(){
          try{
              $data = Db::name('clawyer')->order('awesome desc')->limit(10)->select();
          }catch(\Exception $e){
              return $this->renderError($e);
          }
          $this->assign('data',$data);
          return $this->fetch();
      }


    /**
     * 数据列表
     * @return mixed|string
     */
      public function lists(){
          try{
              $data = Db::name('clawyer')->order('weights desc , awesome desc')->select();
              $comment = db('note')->where('tid','=',8)->order('id desc')->select();
          }catch(\Exception $e){
              return $this->renderError($e);
          }
          $this->assign('uid',session('uid'));
          $this->assign('data',$data);
          $this->assign('comment',$comment);
          return $this->fetch();
      }



      public function unlock($id=''){
          try{
              $data = Db::name('clawyer')->where(['id'=>$id])->find();
              Db::name('clawyer')->where('id','=',$id)->setInc('browse');
          }catch(\Exception $e){
              return $this->renderError($e);
          }
          $this->assign('data',$data);
          return $this->fetch();
      }

}