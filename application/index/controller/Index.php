<?php
namespace app\index\controller;

use app\common\base\AuthController;
use app\common\base\Controllers;
use app\common\base\Mapper;
use app\common\base\PublicMethod;
use app\index\logic\AgentPriceLogicQuery;
use app\index\logic\IndexLogicQuery;
use app\index\logic\LogoLogicQuery;
use helper\Rsas;
use think\Db;
use think\Exception;
use app\common\base\Jssdk;

class Index extends Controllers
{


    /**
     * 查询页面
     * @return mixed|string
     */
    public function index()
    {
        $params = $this->request->param();
        if(isset($params['sign'])){
            $data = Rsas::getInstance()->decode($params['sign']);
            parse_str($data,$params);
        }else{
            $params = ['pid'=>'','uid'=>''];
        }
        try{
            $gonggao = LogoLogicQuery::getInstance()->gonggao(['0','3']);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        $this->assign('pid',$params['pid']);
        $this->assign('uid',$params['uid']);
        $this->assign('gonggao',$gonggao);
        return $this->fetch();
    }


    /**
     * 跳转页面
     * @return mixed|void
     */
    public function paid(){
        try{
            $post = $this->request->param();
            if(isset($post['sign'])){
                $data = Rsas::getInstance()->decode($post['sign']);
                parse_str($data,$params);
            }else{
                throw new Exception('违规操作',1);
            }

            $par = IndexLogicQuery::getInstance()->condition_paid($params);
        }catch(\Exception $e) {
            return $this->html_404($this->renderError($e));
        }
        $this->assign('sign',$post['sign']);
        $this->assign('data',$params);
        $this->assign('price',$par);
        return $this->fetch();
    }


    /**
     * 数据查询
     * @return mixed|string
     */
    public function findOne(){
        try{
            $post = $this->request->param();
            $data = IndexLogicQuery::getInstance()->condition($post);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        if(isset($data) && !empty($data)){
            $sign = Rsas::getInstance()->encode(http_build_query(['cid'=>$data['cid'],'cname'=>$data['name'],'pid'=>$data['pid'],'logo'=>$data['logo']]));
            return $this->renderSuccess('',['redirect'=>'/index/index/paid?sign='.$sign]);
        }else{
            $b_img = Db::name('banner')->field('id,names,thumb')->where('names','=','logo')->find();
            IndexLogicQuery::getInstance()->fault($post);
            return $this->renderSuccess('',['redirect'=>'/index/index/del',['img'=>$b_img['thumb']]]);
        }
    }


    /**
     * 详情
     * @return mixed|string
     */
    public function detail(){
        try{
            $get = $this->request->param();
            if(isset($get['signs']) && !empty($get['signs'])){
                $data = Rsas::getInstance()->decode($get['signs']);
                parse_str($data,$get);
            }
            
            if(isset($get['out_trade_no']) && !empty($get['out_trade_no'])){
                $order = Db::name('Order')->where(['number_order'=>$get['out_trade_no'],'status'=>1])->find();
                if(empty($order)){
                    throw new Exception('订单未支付',1);
                }
            }else{
                    throw new Exception('异常操作',1);
            }
            $data = IndexLogicQuery::getInstance()->findOne(['id'=>$get['id']],'c.*');
            $comment = Db::name('Comment')->where(['cid'=>$data['id'],'isstop'=>0])->select();
            $b_img = Db::name('banner')->field('id,names,thumb')->where('names','=','logo')->find();
            $this->assign('img',$b_img['thumb']);
            $this->assign('data',$data);
            $this->assign('comment',$comment);
            $this->assign('count',count($comment));
        }catch(\Exception $e){
            return $this->html_404($this->renderError($e));
        }
        return $this->fetch();
    }


    /**
     * 详情
     * @return mixed|string
     */
    public function details(){
        try{
            $get = $this->request->param();

            $get['sign'] = '60a53731a8594f330e922c6771f24d7f';
            if(isset($get['sign']) && !empty($get['sign'])){
                $data = Rsas::getInstance()->decode($get['sign']);
                parse_str($data,$get);
            }

            /*if(isset($get['order_id']) && !empty($get['order_id'])){
                $order = Db::name('Order')->where(['id'=>$get['order_id'],'status'=>1])->find();
                if(empty($order)){
                    throw new Exception('订单未支付',1);
                }
            }else{
                    throw new Exception('异常操作',1);
            }*/

            $data = IndexLogicQuery::getInstance()->findOne(['id'=>$get['id']],'c.*');
            $comment = Db::name('Comment')->where(['cid'=>$data['id']])->select();
            $b_img = Db::name('banner')->field('id,names,thumb')->where('names','=','logo')->find();
            $this->assign('data',$data);
            $this->assign('img',$b_img['thumb']);
            $this->assign('comment',$comment);
        }catch(\Exception $e){
            return $this->html_404($this->renderError($e));
        }
        return $this->fetch();
    }

    /**
     * 详情
     * @return mixed
     */
    public function del(){
        return $this->fetch();
    }


    /**
     * 订单查询
     * @return mixed|string
     */
    public function one(){
        try{
            $get = $this->request->param();
            $order = Db::name('Order')->where(['number_order'=>$get['out_trade_no'],'status'=>1])->find();
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        if(!empty($order)){
            $sign = Rsas::getInstance()->encode(http_build_query(['id'=>$order['cid'],'out_trade_no'=>$get['out_trade_no']]));
            return $this->renderSuccess('',['signs'=>$sign]);
        }
    }



}
