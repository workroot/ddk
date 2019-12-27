<?php

namespace app\index\logic;


use app\common\base\LogicQuery;
use think\Db;

class ProvinceLogicQuery extends LogicQuery{


    /**
     * 获取省份
     * @param null $params
     * @throws \Exception
     */
    public function province($params = null){
        try{
            if(empty($params)){
                $params = $this->request->param();
            }

            if(isset($params['pid']) && !empty($params['pid'])){
                $pid = $params['pid'];
            }else{
                $pid = 0;
            }
            $province = Db::name('city')->field('id as value,city_name as text')->where('pid','=',$pid)->select();
            $this->supplement($province);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return json($province);
    }



    public function supplement(&$data){
          foreach($data as &$item){
              $item['children'] = $this->city(['pid'=>$item['value']]);
        }
    }




    /**
     * 获取城市
     * @param null $params
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \Exception
     */
    public function city($params = null){
        try{
            if(empty($params)){
                $params = $this->request->param();
            }

            if(isset($params['pid']) && !empty($params['pid'])){
                    $pid = $params['pid'];
            }else{
                $pid = 0;
            }
            $city = Db::name('city')->field('id as value,city_name as text')->where('pid','=',$pid)->select();
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $city;
    }







}