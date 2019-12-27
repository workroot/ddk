<?php

namespace app\backend\base;


use app\common\base\Controllers;
use org\Auth;
use think\Db;
use think\Loader;
use think\Session;
use helper\Assistant;

class AuthController extends Controllers {

    protected function _initialize()
    {
        parent::_initialize();
        $this->checkAuth();
        $this->getMenu();
        // 输出当前请求控制器（配合后台侧边菜单选中状态）
        $this->assign('controller', Loader::parseName($this->request->controller()));
    }


    /**
     * 权限检查
     * @return mixed
     */
    protected function checkAuth(){
        if(!Session('admin_id')){
            return $this->redirect('/backend/login/index');
        }
        $module = $this->request->module();
        $controller = $this->request->controller();
        $action = $this->request->action();
        // 排除权限
        $not_check = ['backend/Index/index', 'backend/AuthGroup/getjson', 'backend/System/clear'];
        if(!in_array($module . '/' . $controller . '/' . $action , $not_check)){
            $admin_id = Session::get('admin_id');
            $auth = new Auth();
            if (!$auth->check($module . '/' . $controller . '/' . $action, $admin_id) && !in_array($admin_id ,[1])) {
                    $this->error('没有权限');
            }
        }
    }

    /**
     * 获取侧边栏菜单
     */
    protected function getMenu(){
        $menu = [];
        $admin_id = Session::get('admin_id');
        $auth = new Auth();
        $auth_rule_list = Db::name('auth_rule')->where('status', 1)->order(['sort' => 'DESC', 'id' => 'ASC'])->select();
        foreach($auth_rule_list as $value){
            if ($auth->check($value['name'], $admin_id) || in_array($admin_id ,[1])) {
                $menu[] = $value;
            }
        }
        $menu = !empty($menu) ? Assistant::array2tree($menu) : [];
        $this->assign('menu', $menu);
    }

}