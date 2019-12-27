<?php

namespace app\backend\logic;


use app\api\logic\CommentLogicQuery;
use app\backend\base\LogicQuery;
use app\common\model\Comment;
use app\common\model\Reply;
use think\Db;
use think\Exception;

class PlatformLogicQuery extends LogicQuery{


    /**
     * 平台数据详情
     * @param null $params
     * @return array
     * @throws \Exception
     */
    public function detail($params = null){
        try{
            if(empty($params)){
                $params = $this->request->param();
            }
            $data = Db::name('company')->where('id',intval($params['id']))->find();
            $comment = Db::name('Comment')->where(['cid'=>$data['id'],'isstop'=>0])->select();
            $b_img = Db::name('banner')->field('id,names,thumb')->where('names','=','logo')->find();
            $result = compact('data','comment','b_img');
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return $result;
    }



    /**
     * 插入评论
     * @param null $params
     * @throws \Exception
     */
    public function save($params = null){
        try{
            if(empty($params)){
                throw new Exception('参数不能为空',1);
            }
            $result = $this->validate($params , 'Comment');
            if($result !== true){
                throw new Exception($result,1);
            }
            $params['uid'] = 0; //用户ID
            $params['uname'] = substr(time(),-4);
            $params['content'] = htmlspecialchars(trim($params['content']));
            $params['isstop'] = 0;
            $params['createdAt'] = time();
            $comment = new Comment();
            $comment->allowField(true)->save($params);
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
    }



    /**
     * 添加回复
     * @param null $params
     * @throws \Exception
     */
    public function reply($params = null){
        self::startTrans();
        try{
            if(empty($params)){
                throw new Exception('参数不能为空',1);
            }
            $result = $this->validate($params,'Reply');
            if($result !== true){
                throw new Exception($result,1);
            }
            $params['uid'] = 0; //用户ID
            $params['uname'] = substr(time(),-4);
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