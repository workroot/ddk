<?php

namespace app\backend\controller;

use app\backend\base\AuthController;
use app\backend\logic\NoteLogicQuery;
use app\common\base\Mapper;
use app\common\model\Note as NoteModel;
use think\Config;
use think\Db;
use think\Session;

/**
 * 文章管理
 * Class AdminNote
 * @package app\admin\controller
 */
class Note extends AuthController
{
    protected $note_model;

    protected function _initialize()
    {
        parent::_initialize();
        $this->note_model = new NoteModel();
    }

    /**
     * 管理
     * @param string $keyword
     * @param int $page
     * @return mixed
     */
    public function index($keyword = '', $page = 1)
    {
        try {
            $data = NoteLogicQuery::getInstance()->indexList(['keyword' => $keyword, 'page' => $page], $this->note_model);
        } catch (\Exception $e) {
            return $this->renderError($e);
        }
        return $this->fetch('index', ['note_list' => $data, 'keyword' => $keyword]);
    }

    /**
     * 笔记笔记
     */
    public function province()
    {
        $id = input('id');

        $note_list = $this->note_model
            ->alias("a")
            ->field("a.id,a.title,a.lasttime,a.descc,a.content")
            ->where('a.id', '=', $id)
            ->find();
        $wenjianlist = db('wenjian')->where('note_id', '=', $id)->order('id desc')->select();
        return $this->fetch('province', ['note_list' => $note_list, 'wenjianlist' => $wenjianlist]);

    }

    /**
     * 添加
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        //地址
        $note_list = Db::name("notetype")->select();
        $articleType = Mapper::$ARTICLE_TYPE;
        $this->assign("articleType", $articleType);
        $this->assign("note_list", $note_list);
        return $this->fetch();
    }


    /**
     * 文章查询
     * @return mixed|string
     */
    public function title()
    {
        try {
            $id = input('id');
            $data = NoteLogicQuery::getInstance()->findOne(['id' => $id], 'id,title,content');
            $data['content'] = htmlspecialchars_decode($data['content']);
        } catch (\Exception $e) {
            return $this->renderError($e);
        }
        return $this->renderSuccess($data);
    }


    /**
     * 保存
     */
    public function save()
    {
        try {
            if ($this->request->isPost()) {
                $data = $this->request->post();
                $status = NoteLogicQuery::getInstance()->save($data, $this->note_model);
            }
        } catch (\Exception $e) {
            return $this->renderError($e);
        }
        return $this->renderSuccess($status, ['redirect' => '/backend/note/index']);
    }


    public function wenjianlist($keyword = '', $page = 1)
    {
        $map = [];
        if ($keyword) {
            $map['a.title'] = ['like', "%{$keyword}%"];
        }
        //注册时间

        $note_list = db('wenjian')
            ->alias("a")
            ->field("a.id,a.dizhi,a.time,a.note")
            ->where($map)
            ->order('descc desc')->paginate(15, false, ['query' => request()->param()]);
        return $this->fetch('wenjianlist', ['note_list' => $note_list, 'keyword' => $keyword]);
    }


    /**
     * 编辑
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        $note = $this->note_model->find($id);
        $note['content'] = htmlspecialchars_decode($note['content']);
        //口子type
        $note_list = Db::name("notetype")->select();
        $articleType = Mapper::$ARTICLE_TYPE;
        $this->assign("articleType", $articleType);
        $this->assign("note_list", $note_list);
        return $this->fetch('edit', ['note' => $note]);
    }


    /**
     * 更新
     * @return mixed|string
     */
    public function update()
    {
        try {
            if ($this->request->isPost()) {
                $data = $this->request->post();
                $status = NoteLogicQuery::getInstance()->update($data, $this->note_model);
            }
        } catch (\Exception $e) {
            return $this->renderError($e);
        }
        return $this->renderSuccess($status, ['redirect' => '/backend/note/index']);
    }


    /**
     * 排序加一
     * @param $id
     */
    public function up($id)
    {
        $note = $this->note_model->find($id);
        $note->descc = $note['descc'] + 1;
        if ($note->save() !== false) {
            $this->success('更新成功');
        } else {
            $this->error('更新失败');
        }

    }

    public function down($id)
    {

        $note = $this->note_model->find($id);
        $note->descc = $note['descc'] - 1;
        if ($note->save() !== false) {
            $this->success('更新成功');
        } else {
            $this->error('更新失败');
        }

    }

    /**
     * 删除
     * @param $id
     */
    public function delete($id)
    {
        if ($this->note_model->destroy($id)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    public function wenjiandel($id)
    {
        $note = db('wenjian')->where('id', '=', $id)->find($id);
        $QIMG = ROOT_PATH . DS . 'public/upload' . DS . $note['urls'];
        //dump($QIMG);die;
        $wenjiandel = db('wenjian')->where('id', '=', $id)->delete();

        if ($wenjiandel) {
            unlink($QIMG);
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    private function new_mid()
    {
        $max = $this->note_model->max("id");
        if (!$max) {
            $max = 0;
        }
        $max++;
        $max += 10000;
        $new_mid = "M00" . $max;
        return $new_mid;
    }

    /*
    *AJAX城市
    */
    public function ajax_city()
    {
        $cid = $_POST["cid"];
        $city_list = Db::name('city')->where(array("pid" => $cid))->select();
        if ($city_list) {
            $str = "";
            foreach ($city_list as $item) {
                $str .= "<option value=\"" . $item["id"] . "\">" . $item["city_name"] . "</option>\r\n";
            }
        } else {
            $str = "<option value=\"\">该区域下暂无下级</option>";
        }
        echo $str;
    }
}