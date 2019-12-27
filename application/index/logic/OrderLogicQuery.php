<?php

namespace app\index\logic;

use app\common\base\LogicQuery;
use think\Exception;

class OrderLogicQuery extends LogicQuery{

    /**
     * 更新数据
     * @param null $cid
     * @throws \Exception
     */
    public function update($params = null){
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            if ($params['trade_status'] == 'TRADE_FINISHED' || $params['trade_status'] == 'TRADE_SUCCESS') {
                $order = db('order')->where('number_order', '=', $params['out_trade_no'])->find();
                if(!empty($order) && $order['status'] != 1){
                    $result = db('Order')->where('id', $order['id'])->update(['status'=>1,'updatedAt' => time()]);
                    db('user')->where('id', $order['uid'])->setInc('pingfeng');
                    if($result){
                        $this->fenyong($order['id']);
                    }
                    
                	exit('success');
                }
            }
             exit('fail'); 
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return isset($result)?$result:'';
    }


    public function fenyong($dingdanid){
        //查询订单
        $order = db('Order')->where('id','=',$dingdanid)->find();

        //查询代理商
        $agent_user = db('User')->where('id','=',$order['proxyid'])->find();

        //版本表查询
        $price = db('AgentPrice')->where('id','=',$order['pid'])->find();

        //授权数据
        $authAgent =  Db('AuthAgent')->where('id','=',$price['a_p_id'])->find();

        //产品信息
        $product = Db('Product')->where('id','=',$authAgent['pid'])->find();

        //计算提成
        if($authAgent){
            $chengben = $authAgent['price'];
            $tichengs = $order['price']/100 - $chengben;
            if($tichengs < 0){
                $tichengs = 0;
            }
        }else{
            $chengben = $product['price'];
            $tichengs = $order['price']/100 - $chengben;
            if($tichengs < 0){
                $tichengs = 0;
            }
        }

        //余额
        $balance = $agent_user['money']+$tichengs;
        //插入提成表
        $datayiji=[
            'order_id' => $order['number_order'],
            'profit_id' => $agent_user['id'],
            'ratio' => $order['price']/100,
            'create_time' => time(),
            'money' => $tichengs,
            'type' => "直推奖励",
            'balance' => $balance,
            'uid' => $order['uid'],
            'aid' => $price['product_type'],
            'cost_price' => $authAgent['price'],
        ];
        $profit_ids = db('profit')->insertGetId($datayiji);

        //判断上级是否有提成权利
        $superior = db('user')->where('id','=',$agent_user['pid'])->find();

        if($agent_user['isStaff'] == 1 && !empty($superior)){
            //员工推广佣金
            $balances = $superior['money'] + $tichengs;
            $dataerji=[
                'order_id' => $order['number_order'],
                'profit_id' => $superior['id'],
                'ratio' => $order['price']/100,
                'create_time' => time(),
                'money' => $tichengs,
                'type' => "员工推广",
                'balance' => $balance,
                'uid' => $order['uid'],
                'aid' => $price['product_type'],
                'cost_price' => $authAgent['price'],
            ];
            $chaxuner = db('profit')->insertGetId($dataerji);
            if($chaxuner){
                $superior_cost['money'] = $balances;
                $superior_cost['total_achievement']=$superior['total_achievement'] + $tichengs;
                db('user')->where('id','=',$superior['id'])->update($superior_cost);
            }
            if($profit_ids){
                $agent_cost['total_achievement'] = $agent_user['total_achievement'] + $tichengs;
                db('user')->where('id','=',$agent_user['id'])->update($agent_cost);
            }
        }else {
            if ($profit_ids) {
                $agent_cost['money'] = $balance;
                $agent_cost['total_achievement'] = $agent_user['total_achievement'] + $tichengs;
                db('user')->where('id', '=', $agent_user['id'])->update($agent_cost);
            }
            if (isset($superior) && !empty($superior) && $agent_user['isStaff'] != 1) {
                //授权数据
                $erjiticheng = Db('AuthAgent')->where('aid', '=', $superior['agent_class'])->where('pid', '=', $product['id'])->find();

                if (isset($erjiticheng) and $erjiticheng['erjiprice'] > 0) {
                    $erjiprice = $erjiticheng['erjiprice'];
                } else {
                    if (isset($product) and $product['commission'] > 0) {
                        $erjiprice = $product['commission'];
                    }
                }

                if (isset($erjiprice) and !empty($erjiprice)) {
                    $balances = $superior['money'] + $erjiprice;
                    $dataerji = [
                        'order_id' => $order['number_order'],
                        'profit_id' => $superior['id'],
                        'ratio' => $order['price'] / 100,
                        'create_time' => time(),
                        'money' => $erjiprice,
                        'type' => "二级奖励",
                        'balance' => $balances,
                        'uid' => $order['uid'],
                        'aid' => $price['product_type'],
                    ];
                    $chaxuner = db('profit')->insertGetId($dataerji);
                    if ($chaxuner) {
                        $moneyer['money'] = $balances;
                        $moneyer['total_achievement'] = $superior['total_achievement'] + $erjiprice;
                        db('user')->where('id', '=', $superior['id'])->update($moneyer);
                    }
                }
            }
        }
    }
}