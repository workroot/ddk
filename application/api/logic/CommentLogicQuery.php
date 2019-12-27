<?php

namespace app\api\logic;

use app\common\base\LogicQuery;
use app\common\model\Comment;
use think\Db;
use think\Exception;
use think\Session;

class CommentLogicQuery extends LogicQuery{


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
            $params['uid'] = Session('uid'); //用户ID
            $params['uname'] = Session('uname');
            $params['content'] = htmlspecialchars($params['content']);
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
     * 查询数据
     * @param null $params
     * @param bool $type
     * @throws \Exception
     */
    public function index($params = null,$type=true){
        try{
            if(empty($params)){
                throw new Exception('参数不能为空');
            }
            $query = Db::name('Comment');
            $this->query($query,$params);
            if($type){
                $data = $query->select();
            }else{
                $data =  $query->find();
            }
            if(!empty($data)){
                $this->reply($data);
            }
        }catch(\Exception $e){
            $this->log($e);
            throw $e;
        }
        return isset($data)?$data:'';
    }


    /**
     * 回复数据
     * @param $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function reply(&$data){
            foreach($data as &$item) {
                $wesome = Db::name('Awesome')->field('id')->where(['uid'=>Session('user_id'),'commentId'=>$item['id']])->where('createdAt','>=',time())->find();
                $item['reply'] = Db::name('Reply')->where(['cid' => $item['cid'], 'commentId' => $item['id']])->select();
                $item['isawe'] = isset($wesome)&&!empty($wesome)?true:false;
            }
    }


    /**
     * 回复自动加一
     * @param $params
     * @return int|true
     * @throws Exception
     */
    public function responsesInc($params){
        return db('Comment')->where('id', $params['id'])->setInc('responses');
    }


    /**
     * 点赞自动加一
     * @param $params
     * @return int|true
     * @throws Exception
     */
    public function awesomeInr($params){
        return db('Comment')->where('id', $params['id'])->setInc('awesome');
    }


    /**
     * 查询条件
     * @param $query
     * @param $params
     */
    public function query($query,$params){
        if(isset($params['cid']) && !empty($params['cid'])){
            $query->where('cid','=',$params['cid']);
        }
        $query->where('isstop','=',0);
        $query->order('createdAt desc');
        if(isset($params['page']) && !empty($params['page'])){
            $limit = isset($params['limit'])?$params['limit']:10;
            $query->page($params['page'],$limit);
        }
    }
}