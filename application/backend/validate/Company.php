<?php

namespace app\backend\validate;

use think\Validate;

/**
 * 权限组验证器
 * Class AdminUser
 * @package app\admin\validate
 */
class Company extends Validate
{
    protected $rule = [
        'name'         => 'require',//|unique:company
        'amount'         => 'require',
        'periods'         => 'require',
        //'phone' => 'require',
    ];

    protected $message = [
        'name.require'         => '平台名称不能为空',
        'name.unique'          => '该平台已存在',
        'amount.require'          => '平台额度不能为空',
        'periods.require'          => '可贷期数不能为空',
       // 'phone.require'          => '联系方式不能为空',
    ];
}