<?php

namespace app\index\controller;

use app\common\base\AuthController;
use app\common\base\Mapper;
use app\index\logic\PromotionLogicQuery;
use helper\Imgdeal;
use think\Db;

class Promotion extends AuthController{




    public function index(){
        return $this->fetch();
    }


    /**
     * 设置推广平台价格
     * @return mixed
     */
    public function addPrice(){
        $price = Db::name('AgentPrice')->where(['uid'=>Session('user_id'),'product_type' => Mapper::PRODUCT_TYPE])->order('createdAt desc')->select();

        $prices = PromotionLogicQuery::getInstance()->version_data();

        return $this->fetch('addprice',['data'=>$price,'prices'=>$prices]);
    }


    /**
     *  添加支付连接
     * @return mixed
     */
    public function add(){
        try{
            $params = $this->request->param();
            $params['uid'] = Session('user_id');
            $params['rename'] = $params['pname'];
            $price = PromotionLogicQuery::getInstance()->price($params);
        }catch(\Exception $e){
            return $this->html_404($this->renderError($e));
        }
        return $this->renderSuccess($price);
    }


    /**
     * 更新
     * @return mixed
     */
    public function edit(){
        try{
            $params = $this->request->param();
            $params['uid'] = Session('user_id');
            $price = PromotionLogicQuery::getInstance()->update($params);
        }catch(\Exception $e){
            return $this->html_404($this->renderError($e));
        }
        return $this->renderSuccess($price);
    }


    /**
     * 添加推广链接
     * @return mixed
     */
    public function addmany(){
        try{
            $params = $this->request->param();
            $price = PromotionLogicQuery::getInstance()->price($params);
        }catch(\Exception $e){
            return $this->html_404($this->renderError($e));
        }
        return $this->renderSuccess($price);
    }


    /**
     * 删除链接
     * @return mixed
     */
    public function del(){
        try{
            $params = $this->request->param();
            $result = PromotionLogicQuery::getInstance()->del($params);
        }catch(\Exception $e){
            return $this->html_404($this->renderError($e));
        }
        return $this->renderSuccess($result);
    }


    public function query(){
        $price = Db::name('AgentPrice')->where(['uid'=>Session('user_id'),'product_type' => Mapper::PRODUCT_TYPE])->order('createdAt desc')->select();
        $prices = PromotionLogicQuery::getInstance()->version_data();
        return $this->fetch('list',['data'=>$price,'prices'=>$prices]);
    }


    /**
     * 律师设置价格
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function layPrice(){
        $price = Db::name('AgentPrice')->where(['uid'=>Session('user_id'),'product_type' => Mapper::PRODUCT_TYPE_TWO])->select();
        return $this->fetch('layprice',['data'=>$price]);
    }


    /**
     * 二维码推广图片处理
     * @param $pid
     */
    public function code_img($pid){
        $b_img = Db::name('banner')->where('names','in',['贷款平台','空白二维码','logo1'])->select();
        $agent_price = Db::name('AgentPrice')->where('id','=',intval($pid))->find();
        $platform_img = '';
        $code_img = '';
        $logo = '';
        if(isset($b_img) && !empty($b_img)){
            foreach($b_img as $item){
                if($item['names'] == '贷款平台'){
                    $platform_img = $item['thumb'];
                }
                if($item['names'] == '空白二维码'){
                    $code_img = $item['thumb'];
                }

                if($item['names'] == 'logo1'){
                    $logo = $item['thumb'];
                }
            }
        }
        $img = Imgdeal::getInstance()->img_conversion($platform_img,$agent_price['url'],$code_img,7,$logo,260,1000);
        echo '<body style="margin:0px;padding:0;"><div style="height:100px;line-height:100px;text-align:center;font-size:40px;background: #ac5cff;"><a href="/index/promotion/index" style=" text-decoration: none;color:#fff;margin:0px;padding:0;">回到主页面</a></div>
                    <img src="/img/' . $img . '" style="width:100%;margin:0px;padding:0;"/>
              </body>';
    }

}