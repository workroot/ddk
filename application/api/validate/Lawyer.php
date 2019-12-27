<?php
namespace app\api\validate;

use think\Validate;

/**
 * 后台登录验证
 * Class Login
 * @package app\admin\validate
 */
class Lawyer extends Validate
{
    protected $rule = [
        'mobile' => 'require',
        'content' => 'require',
    ];

    protected $message = [
        'mobile.require' => '请输入用户手机号码',
        'content.require' => '请输入咨询内容',
    ];
}