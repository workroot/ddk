<?php

namespace app\calculator\base;

use think\Log;

class Logger{

    /**
     * 记录日志
     * @param string|array $message
     * @param number $level
     * @param string $category
     */
    public static function log($message,$category = 'app') {
        if (!empty($message)) {
            if ($message instanceof \Exception) {
                $error = [$message->getCode(), $message->getMessage(), $message->getFile(), $message->getLine()];
                $message = [implode(', ', $error), $message->getTraceAsString()];
            }
        }
        Log::record($message,'error');
    }



}