<?php
namespace app\api\logic;


use app\common\base\LogicQuery;
use app\common\model\Awesome;
use think\Exception;

class AwesomeLogicQuery extends LogicQuery{


    /**
     * 点赞
     * @param null $params
     * @throws \Exception
     */
    public function save($params = null){
        self::startTrans();
        try{
            if(empty($params)){
                throw new Exception('参数不能为空',1);
            }
            $params['uid'] = Session('uid');
            $params['createdAt'] = strtotime('+2day',time());
            $awesome = new Awesome();
            $awesome->allowField(true)->save($params);
            CommentLogicQuery::getInstance()->awesomeInr(['id'=>intval($params['commentId'])]);
            self::commit();
        }catch(\Exception $e){
            $this->log($e);
            self::rollback();
            throw $e;
        }
    }
}