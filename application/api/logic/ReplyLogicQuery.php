<?php

namespace app\api\logic;


use app\common\base\LogicQuery;
use app\common\model\Reply;
use think\Exception;

class ReplyLogicQuery extends LogicQuery{


    /**
     * 添加数据
     * @param null $params
     * @throws \Exception
     */
    public function save($params = null){
        self::startTrans();
        try{
            if(empty($params)){
                throw new Exception('参数不能为空',1);
            }
            $result = $this->validate($params,'Reply');
            if($result !== true){
                throw new Exception($result,1);
            }
            $params['uid'] = Session('uid');
            $params['uname'] = Session('uname');
            $params['isstop'] = 1;
            $params['createdAt'] = time();
            $comment = new Reply();
            $comment->allowField(true)->save($params);
            CommentLogicQuery::getInstance()->responsesInc(['id'=>intval($params['commentId'])]);
            self::commit();
        }catch(\Exception $e){
            $this->log($e);
            self::rollback();
            throw $e;
        }
    }
}