<?php
namespace app\backend\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'mobile'           => 'require|number|length:11|unique:user',
        'status'           => 'require',
    ];

    protected $message = [
        'mobile.require'            => '请输入手机号',
        'mobile.unique'            => '手机号已存在',
        'mobile.length'            => '手机号长度错误',
        'status.require'           => '请选择状态'
    ];
}