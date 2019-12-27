<?php

namespace app\index\controller;

use app\common\base\Controllers;
use app\common\base\PublicMethod;
use app\index\logic\LawyerLogicQuery;

class Lawyer extends Controllers {


    /**
     * 律师咨询
     * @return mixed|string
     */
      public function index(){
          try{
              $post = $this->request->param();
              $data = LawyerLogicQuery::getInstance()->condition($post);
          }catch(\Exception $e){
              return $this->renderError($e);
          }
          $this->assign('data',$data);
          return $this->fetch();
      }

    /**
     * @return mixed
     */
    public function feedback(){
        return $this->fetch();
    }

    /**
     * 金额页面
     * @return mixed
     */
    public function payment(){
        try{
            $post = $this->request->param();
        }catch(\Exception $e){
            return $this->html_404($this->renderError($e));
        }
        $pcount = Db('Lawyer')->where('isPay','=',0)->count();
        $this->assign('pcount',$pcount);
        $this->assign('price',$post['price']);
        $this->assign('pid',$post['pid']);
        $this->assign('lid',$post['lid']);
        return $this->fetch();
    }


    public function question(){
        return $this->fetch();
    }



}