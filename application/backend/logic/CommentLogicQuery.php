<?php

namespace app\backend\logic;


use app\common\base\LogicQuery;
use think\Db;

class CommentLogicQuery extends LogicQuery{


    /**
     * 评论列表
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
            if(isset($params['keyword']) && !empty($params['keyword'])){
                if($params['keyword'] == '总部'){
                    $map = ['l.uid' => 0];
                }else{
                    $map = [
                        'c.name|l.uname'=>['like',"%{$params['keyword']}%"],
                    ];
                }
            }
            if(isset($params['start_time']) && !empty($params['start_time'])){
                $map = ['>=','l.createdAt',$params['start_time']];
            }

            if(isset($params['end_time']) && !empty($params['end_time'])){
                $map = ['<=','l.createdAt',$params['end_time']];
            }
            $data = Db::name('comment')->alias('l')->field('l.*,c.name')->join('__COMPANY__ c','l.cid = c.id','LEFT')->where($map)->order('l.createdAt DESC')->paginate(15, false, ['query'=>request()->param()]);
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
        $a = 0;
        foreach($data as &$item){
            $item['signs'] = Rsas::getInstance()->encode(http_build_query(['id'=>$item['cid'],'out_trade_no'=>$item['number_order']]));
            $item['createdAt'] = date('Y-m-d H:i:s',$item['createdAt']);
        }
    }

}