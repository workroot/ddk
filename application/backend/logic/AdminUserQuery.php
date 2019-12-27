<?php

namespace app\backend\logic;

use app\backend\base\LogicQuery;
use app\common\model\AdminUser;
use app\common\model\AuthGroupAccess;
use think\Config;
use think\Db;
use think\Exception;

class AdminUserQuery extends LogicQuery {

    /**
     * 添加管理员用户
     * @param null $params
     * @throws \Exception
     */
    public function save($params = null){
        self::startTrans();
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            $result = $this->validate($params,'AdminUser');
            if($result !== true){
                throw new Exception($result,1);
            }
            $params['password'] = md5($params['password'] . Config::get('salt'));
            $adminUser = new AdminUser();
            if($adminUser->allowField(true)->save($params)){
                AuthGroupAccess::create(['uid'=>$adminUser->id,'group_id'=>$params['group_id']]);
            }
            self::commit();
        }catch(\Exception $e){
            $this->log($e);
            self::rollback();
            throw $e;
        }
    }


    /**
     * 管理员更新
     * @param null $params
     * @throws \Exception
     */
    public function update($params = null){
        self::startTrans();
        try{
            if(empty($params)){
                throw new Exception('参数错误',1);
            }
            $result = $this->validate($params,'AdminUpdateUser');
            if($result !== true){
                throw new Exception($result,1);
            }

            $admin_user = AdminUser::find($params['id']);
            if(empty($admin_user)){
                throw new Exception('参数错误',1);
            }
            if (!empty($params['password']) && !empty($params['confirm_password'])) {
                $admin_user->password = md5($params['password'] . Config::get('salt'));

            }
            $admin_user->id = $params['id'];
            $admin_user->username = $params['username'];
            $admin_user->status   = $params['status'];
            $admin_user->names    = $params['names'];
            if($admin_user->save() !== false){
                AuthGroupAccess::update(['uid'=>$admin_user->id,'group_id'=>$params['group_id']]);
            }
            self::commit();
        }catch(\Exception $e){
            $this->log($e);
            self::rollback();
            throw $e;
        }
    }

}