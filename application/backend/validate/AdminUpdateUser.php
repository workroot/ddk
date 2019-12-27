<?php
namespace app\backend\validate;

use think\Validate;

/**
 * 管理员验证器
 * Class AdminUser
 * @package app\admin\validate
 */
class AdminUpdateUser extends Validate
{
    protected $rule = [
        'username'         => 'require',
        'password'         => 'require',
        'confirm_password' => 'requireWith:confirm_password|confirm:password',
        'status'           => 'require',
        'group_id'         => 'require'
    ];

    protected $message = [
        'username.require'         => '请输入用户名',
        'password'                 => '请输入密码',
        'confirm_password.confirm'         => '两次输入密码不一致',
        'status.require'           => '请选择状态',
        'group_id.require'         => '请选择所属权限组'
    ];
}