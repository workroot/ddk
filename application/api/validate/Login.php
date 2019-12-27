<?php
namespace app\api\validate;

use think\Validate;

/**
 * 后台登录验证
 * Class Login
 * @package app\admin\validate
 */
class Login extends Validate
{
    protected $rule = [
        'mobile' => 'require|unique:user',
        'password'         => 'confirm:password_confirm',
        'password_confirm' => 'confirm:password',
    ];

    protected $message = [
        'mobile.require' => '请输入用户手机号码',
        'mobile.unique'          => '该手机已注册',
        'password.require' => '请输入密码',
        'password.confirm'         => '两次输入密码不一致',
        'password_confirm.confirm' => '两次输入密码不一致',
    ];
}