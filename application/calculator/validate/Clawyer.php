<?php

namespace app\calculator\validate;

use think\Validate;

/**
 * 管理员验证器
 * Class AdminUser
 * @package app\admin\validate
 */
class Clawyer extends Validate
{
    protected $rule = [
        'title'         => 'require',
        'content'         => 'require',
        'price' => 'require',
    ];

    protected $message = [
        'title.require'         => '请输入标题',
        'content.require'          => '请输入内容',
        'price.require'         => '请输入支付金额',
    ];
}