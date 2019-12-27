<?php

namespace app\backend\controller;


use app\backend\base\AuthController;
use app\backend\logic\CommentLogicQuery;
use app\common\model\Comment as CommentModel;
use app\common\model\Reply as ReplyModel;
use think\Db;

class Comment extends AuthController{

    protected $comment_model;
    protected $ReplyModel;

    protected function _initialize()
    {
        parent::_initialize();
        $this->comment_model = new CommentModel();
        $this->ReplyModel = new ReplyModel();
    }


    /**
     * 评论列表
     * @return mixed|string
     */
    public function index(){
        try{
            $get = $this->request->param();
            $data = CommentLogicQuery::getInstance()->query();
            $this->assign('get',$get);
            $this->assign('data',$data);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->fetch();
    }


    /**
     * 评论删除
     * @param string $id
     */
    public function delete($id = ''){
        if ($this->comment_model->destroy($id)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }


    /**
     *  获取回复数据
     * @return mixed|string
     */
    public function subdirectory(){
        try{
            $get = $this->request->param();
            $data= Db::name('Reply')->where(['cid' => $get['cid'], 'commentId' => $get['id']])->select();
            $this->assign('data',$data);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->fetch();
    }


    /**
     * 评论删除
     * @param string $id
     */
    public function del(){
        $get = $this->request->param();
        db('Comment')->where('id', $get['cid'])->setDec('responses');
        if ($this->ReplyModel->destroy($get['id'])) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }


}