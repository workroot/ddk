<?php
namespace app\api\validate;

use think\Validate;

/**
 * 后台登录验证
 * Class Login
 * @package app\admin\validate
 */
class Comment extends Validate
{
    protected $rule = [
        'content' => 'require|max:500',
    ];

    protected $message = [
        'content.require' => '评论内容不能为空',
        'content.max' => '评论内容不能超过500文字',
    ];
}