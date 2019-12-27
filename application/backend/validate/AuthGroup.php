<?php
namespace app\backend\validate;

use think\Validate;

/**
 * 权限组验证器
 * Class AdminUser
 * @package app\admin\validate
 */
class AuthGroup extends Validate
{
    protected $rule = [
        'title'         => 'require|unique:auth_group',
    ];

    protected $message = [
        'title.require'         => '组名称不能为空',
        'title.unique'          => '该组已存在',
    ];
}