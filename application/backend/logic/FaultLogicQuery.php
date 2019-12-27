<?php

namespace app\backend\logic;

use app\common\base\LogicQuery;
use think\Db;

class FaultLogicQuery extends LogicQuery{


    /**
     * 过失数据列表
     * @param null $params
     * @return \think\Paginator
     * @throws \Exception
     */
    public function query($params = null){
        try{
            if(empty($params)){
                $params = $this->request->param();
            }
            $map = [];
            if(isset($params['keyWord']) && !empty($params['keyWord'])){
                $map = [
                    'mobile|title'=>['like',"%{$params['keyWord']}%"],
                ];
            }

            if(isset($params['start_time']) && !empty($params['start_time'])){
                $map = ['>=','createdAt',$params['start_time']];
            }

            if(isset($params['end_time']) && !empty($params['end_time'])){
                $map = ['<=','createdAt',$params['end_time']];
            }
            $data = Db::name('Fault')->where($map)->order(['createdAt'=>'DESC'])->paginate(15, false, ['query'=>request()->param()]);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $data;
    }


}