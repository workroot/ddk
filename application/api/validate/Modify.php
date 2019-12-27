<?php
namespace app\api\validate;

use think\Validate;

/**
 * 后台登录验证
 * Class Login
 * @package app\admin\validate
 */
class Modify extends Validate
{
    protected $rule = [
        'mobile' => 'require',
        'password' => 'confirm:passwords',
        'passwords' => 'confirm:password',
    ];

    protected $message = [
        'mobile.require' => '请输入用户手机号码',
        'password.require' => '请输入密码',
        'password.confirm'         => '两次输入密码不一致1',
        'passwords.confirm' => '两次输入密码不一致2',
    ];
}