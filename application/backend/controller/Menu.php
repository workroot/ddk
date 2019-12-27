<?php
namespace app\backend\controller;

use app\backend\base\AuthController;
use app\backend\logic\MenuLogicQuery;
use app\common\model\AuthRule as AuthRuleModel;
use helper\Assistant;

/**
 * 后台菜单
 * Class Menu
 * @package app\admin\controller
 */
class Menu extends AuthController
{

    protected $auth_rule_model;

    protected function _initialize()
    {
        parent::_initialize();
        $this->auth_rule_model = new AuthRuleModel();
        $admin_menu_list       = $this->auth_rule_model->order(['sort' => 'DESC', 'id' => 'ASC'])->select();
        $admin_menu_level_list = Assistant::array2level($admin_menu_list);

        $this->assign('admin_menu_level_list', $admin_menu_level_list);
    }

    /**
     * 后台菜单
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 添加菜单
     * @param string $pid
     * @return mixed
     */
    public function add($pid = '')
    {
        return $this->fetch('add', ['pid' => $pid]);
    }

    /**
     * 保存菜单
     */
    public function save()
    {
        if ($this->request->isPost()) {
            try{
                $data = $this->request->param();
                $status = MenuLogicQuery::getInstance()->save($data,$this->auth_rule_model);
            }catch(\Exception $e){
                return $this->renderError($e);
            }
            return $this->renderSuccess($status,['redirect'=>'/backend/menu/index']);
        }
    }

    /**
     * 编辑菜单
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $admin_menu = $this->auth_rule_model->find($id);

        return $this->fetch('edit', ['admin_menu' => $admin_menu]);
    }


    /**
     * 菜单更新
     * @return mixed|string
     */
    public function update()
    {
        if ($this->request->isPost()) {
            try{
                $data = $this->request->param();
                $status = MenuLogicQuery::getInstance()->update($data,$this->auth_rule_model);
            }catch(\Exception $e){
                return $this->renderError($e);
            }
            return $this->renderSuccess($status,['redirect'=>'/backend/menu/index']);
        }
    }

    /**
     * 删除菜单
     * @param $id
     */
    public function delete($id)
    {
        $sub_menu = $this->auth_rule_model->where(['pid' => $id])->find();
        if (!empty($sub_menu)) {
            $this->error('此菜单下存在子菜单，不可删除');
        }
        if ($this->auth_rule_model->destroy($id)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
}