<?php

namespace app\backend\controller;

use app\backend\base\AuthController;
use app\backend\logic\UserAgentLogicQuery;
use app\common\model\User as UserModel;
use think\Db;

class Useragent extends AuthController{
    protected $user_agent_model;

    protected function _initialize()
    {
        parent::_initialize();
        $this->user_agent_model = new UserModel();

    }


    /**
     * 代理类型类别
     * @param string $keyword
     * @param int $page
     * @return mixed|string
     */
    public function index($keyword = '', $page = 1,$startTime='',$endTime='',$uid=''){
        try{
            $total_achievement = input('total_achievement');
            $list = UserAgentLogicQuery::getInstance()->agentList(['keyword'=>$keyword,'total_achievement'=>$total_achievement,'page'=>$page,'startTime'=>$startTime,'endTime'=>$endTime,'uid'=>$uid],$this->user_agent_model);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        $list['keyword'] = $keyword;
        $list['total_achievement'] = $total_achievement;
        $list['startTime'] = $startTime;
        $list['endTime'] = $endTime;
        return $this->fetch('index',$list);
    }


    /**
     * 添加页面
     * @return mixed
     */
    public function add(){
        $agent = db('agent')->where('id','<>',1)->field('id,agent_name')->select();
        $this->assign('agent_list',$agent);
        return $this->fetch();
    }


    /**
     * 提交
     * @return mixed|string
     */
    public function save(){
        try{
            $result = UserAgentLogicQuery::getInstance()->save('',$this->user_agent_model);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->renderSuccess($result,['redirect'=>'/backend/useragent/index']);
    }


    /**
     * 编辑数据
     * @return mixed|string
     */
    public function edit(){
        try{
            $agent = db('agent')->where('id','<>',1)->field('id,agent_name')->select();
            $data = UserAgentLogicQuery::getInstance()->findOne('',$this->user_agent_model);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        $aid = Session('admin_id');
        $user_list = Db::name("user")->where("agent_class",">","1")->select();
        $this->assign("user_list",$user_list);
        $this->assign('agent_list',$agent);
        $this->assign('user',$data);
        $this->assign('aid',$aid);
        return $this->fetch();
    }


    /**
     * 更新数据
     * @return mixed|string
     */
    public function update(){
        try{
            $result = UserAgentLogicQuery::getInstance()->updated('',$this->user_agent_model);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->renderSuccess($result,['redirect'=>'/backend/useragent/index']);
    }

    /**
     * 查看下级列表
     * @return userlist
     */
    public function selxia($id,$page = 1)
    {
        $map = [];
        $map['a.pid'] = $id;
        $user_list = $this->user_agent_model
            ->alias("a")
            ->field("a.*,c.names pnames")
            ->where($map)
            ->join("__USER__ c",'a.pid=c.id','LEFT')
            ->order('id DESC')->paginate(15, false, ['query'=>request()->param()]);
        $count=$this->user_agent_model->alias("a")->where($map)->count('a.id');
        $map1=[];
        $map1['a.agent_class'] =  ['>', "1"];
        $map2=[];
        $map2['a.agent_class'] =  ['=', "1"];
        $countdailishang=$this->user_agent_model->alias("a")->where($map)->where($map1)->count('a.id');
        $countputong=$this->user_agent_model->alias("a")->where($map)->where($map2)->count('a.id');

        return $this->fetch('selxia', ['user_list' => $user_list,'count' => $count ,'countdailishang' => $countdailishang ,'countputong' => $countputong ]);
    }


    /**
     *  更新是否添加微信
     */
    public function iswx($id){
        if ($this->request->isPost()) {
            $iswx = input('iswx');
            $user           = $this->user_agent_model->find($id);
            $user->id       = $id;
            $user->iswx = $iswx;
            if ($user->save() !== false) {
                $this->success('更新成功');
            } else {
                $this->error('更新失败');
            }
        }
    }


    /**
     * 删除
     * @param $id
     */
    public function delete($id){
        if ($this->user_agent_model->destroy($id)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
}
