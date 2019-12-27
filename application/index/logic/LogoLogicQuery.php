<?php

namespace app\index\logic;

use app\common\base\LogicQuery;

class LogoLogicQuery extends LogicQuery{


    /**
     * 获取logo图
     * @return string
     * @throws \Exception
     */
       public function logo(){
           try{
               $logo = Db('banner')->where('type','=',2)->order('id desc')->find();
           }catch(\Exception $e){
                $this->log($e);
                throw $e;
           }

           return isset($logo) && !empty($logo['thumb']) ? $logo['thumb'] : '';
       }


    /**
     * 获取公告
     * @param $type
     * @return string
     * @throws \Exception
     */
       public function gonggao($type){
           try{
               $gonggao = Db('gonggao')->where('type','in',$type)->order('id desc')->select();
           }catch(\Exception $e){
               $this->log($e);
               throw $e;
           }
           return isset($gonggao) && !empty($gonggao) ? $gonggao : '';
       }
}