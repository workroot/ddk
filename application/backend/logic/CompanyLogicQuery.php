<?php

namespace app\backend\logic;

use app\backend\base\LogicQuery;
use think\Db;
use think\Exception;

class CompanyLogicQuery extends LogicQuery{

    /**
     * 多数据查询
     * @param null $params
     * @param string $select
     * @return \think\Paginator
     * @throws \Exception
     */
    public function findAll($params = null,$select="c.*"){
        try{
            $query = Db::name('Company');
            $query->alias('c');
            $query->field($select);
            $this->params($params,$query);

            if(isset($params['flow']) && $params['flow'] == 1){
                $desc = 'flow DESC';
            }else{
                $desc = 'id DESC';
            }
            $data = $query->order($desc)->paginate(15, false, ['query'=>request()->param()]);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $data;
    }


    /**
     * 添加公司数据
     * @param null $params
     * @param $object
     * @throws \Exception
     */
    public function save($params=null,$object){
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            $result =  $this->validate($params, 'Company');
            if ($result !== true) {
                throw new Exception($result,1);
            }
            $params['status'] = 2;
            $params['uname'] = '官方';
            $params['isdel'] = 1;
            $params['createdAt'] = time();
            $object->allowField(true)->save($params);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
    }


    public function saveAll($params=null){
        try{
            if(empty($params)){
                return true;
            }
            $num = 1000;
            $limit = ceil(count($params)/1000);
            for($i=1;$i<=$limit;$i++){
                $offset = ($i-1)*$num;
                $data = array_slice($params,$offset,$num);
                $status = Db::name('Company')->insertAll($data,true);
            }
            if(!$status){
                throw new Exception('数据格式错误',1);
            }
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return isset($status)?true:false;
    }


    public function update($params=null,$object){
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            $result =  $this->validate($params, 'Company');
            if ($result !== true) {
                throw new Exception($result,1);
            }
            $params['updatedAt'] = time();
            if ($object->allowField(true)->save($params,$params['id']) === false) {
                throw new Exception('跟新失败',1);
            }
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
    }

    /**
     * 查询参数
     * @param null $params
     * @return array
     */
    public function params($params=null,$query){
        if (isset($params['keyword']) && is_numeric($params['keyword']) && !empty($params['keyword'])) {
            $query->where('c.id','=',$params['keyword']);
        }
        if(isset($params['keyword']) && !is_numeric($params['keyword']) && !empty($params['keyword'])){
            $query->where('c.name|c.mechanism|c.uname','like',"%{$params['keyword']}%");
        }
        if(isset($params['start_time']) && !empty($params['start_time'])){
            $startime = strtotime($params['start_time']);
            $query->where('c.createdAt','>=',$startime);
        }

        if(isset($params['end_time']) && !empty($params['end_time'])){
            $endtime = strtotime($params['end_time']);
            $query->where('c.createdAt','<=',$endtime);
        }
        if(isset($params['uid']) && !empty($params['uid'])){
            $query->where('c.uid','=',$params['uid']);
        }
        if(isset($params['platformType']) && !empty($params['platformType'])){
            $query->where('c.platformType','=',$params['platformType']);
        }
        $query->where('c.isdel','=',1);
    }
}