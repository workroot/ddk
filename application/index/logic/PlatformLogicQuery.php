<?php

namespace app\index\logic;


use app\common\base\LogicQuery;
use app\common\model\Order;
use helper\Rsas;
use think\Collection;

class PlatformLogicQuery extends LogicQuery{

    protected $resultSetType = 'collection';

    /**
     * 客户列表
     * @param null $params
     * @return array
     * @throws \Exception
     */
    public function query($params = null){
        try{
            if(empty($params)){
                $params = $this->request->param();
            }
            $map = [];
            if(isset($params['start_time']) && !empty($parmas['start_time'])){
                $map = ['>=','createdAt',$params['start_time']];
            }

            if(isset($params['end_time']) && !empty($params['end_time'])){
                $map = ['<=','createdAt',$params['end_time']];
            }

            if(isset($params['keyWord']) && !empty($params['keyWord'])){
                $map = ['name'=>['like',"%{$params['keyWord']}%"]];
            }

            if(isset($params['type']) && $params['type'] == 1){
                $data = $this->client_list($map);

            }else{

            }
            $this->fillQueryList($data['data']);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $data;
    }


    /**
     * 数据转换
     * @param $data
     */
    public function fillQueryList(&$data){
        foreach($data as &$item){
            $item['signs'] = Rsas::getInstance()->encode(http_build_query(['id'=>$item['cid'],'out_trade_no'=>$item['number_order']]));
            $item['createdAt'] = date('Y-m-d H:i:s',$item['createdAt']);
        }
    }


    /**
     * 客户查询记录
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function client_list($params){
            try{
                $query = new Order();
                $data = $query
                    ->alias('o')
                    ->field('o.id , o.cid , o.uid , o.number_order , o.price , o.createdAt , c.name , c.amount , c.periods , c.interest , c.phone , c.score , u.names , u.mobile')
                    ->join('__COMPANY__ c','o.cid = c.id','LEFT')
                    ->join('__USER__ u','o.uid = u.id','LEFT')
                    ->where($params)
                    ->where(['o.status'=>'1'])
                    ->where(['o.uid' => session('uid')])
                    ->order(['o.createdAt' => 'DESC'])
                    ->paginate(15, false,['query'=>request()->param()]);
            }catch(\Exception $e){
                $this->log($e);
                throw $e;
            }
            return $data->toArray();
    }

}
