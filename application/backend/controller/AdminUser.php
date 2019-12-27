<?php
namespace app\backend\controller;

use app\backend\base\AuthController;
use app\backend\logic\AdminUserQuery;
use think\Config;
use think\Db;

/**
 * 管理员管理
 * Class AdminUser
 * @package app\admin\controller
 */
class AdminUser extends AuthController
{


    /**
     * 管理员列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $admin_user_list = Db::name('admin_user')->select();
        return $this->fetch('index', ['admin_user_list' => $admin_user_list]);
    }


    /**
     * 添加管理员
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        $auth_group_list = Db::name('auth_group')->select();
        return $this->fetch('add', ['auth_group_list' => $auth_group_list]);
    }

    /**
     * 添加管理员
     * @return mixed|string
     */
    public function save()
    {
        if ($this->request->isPost()) {
            try{
                $data = $this->request->param();
                $status = AdminUserQuery::getInstance()->save($data);
            }catch(\Exception $e){
                return $this->renderError($e);
            }
            return $this->renderSuccess($status,['redirect'=>'/backend/admin_user/index']);
        }
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
        $admin_user             = Db::name('admin_user')->find($id);
        $auth_group_list = Db::name('auth_group')->select();
        $auth_group_access      = Db::name('auth_group_access')->where('uid', $id)->find();
        $admin_user['group_id'] = $auth_group_access['group_id'];
        return $this->fetch('edit', ['admin_user' => $admin_user, 'auth_group_list' => $auth_group_list]);
    }

    /**
     * 更新管理员
     * @param $id
     * @param $group_id
     * @return mixed|string
     */
    public function update($id, $group_id)
    {
        if ($this->request->isPost()) {
            try {
                $data = $this->request->param();
                $status = AdminUserQuery::getInstance()->update($data);
            } catch (\Exception $e) {
                return $this->renderError($e);
            }
            return $this->renderSuccess($status, ['redirect' => '/backend/admin_user/index']);
        }
    }

    /**
     * 删除管理员
     * @param $id
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delete($id)
    {
        if ($id == 1) {
            $this->error('默认管理员不可删除');
        }
        if (\app\common\model\AdminUser::destroy($id)) {
            db('auth_group_access')->where('uid', $id)->delete();
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
}