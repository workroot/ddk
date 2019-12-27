<?php

namespace app\calculator\base;

use think\Controller;
use think\Db;

abstract class LogicQuery extends Controller{

    protected static $instance = [];
    /**
     * 单列
     * @return static|self
     */
    public static function getInstance(){
        $className = get_called_class();
        if(!isset(static::$instance[$className])){
            static::$instance[$className] = new $className;
        }
        return static::$instance[$className];
    }



    /**
     * 添加错误日志
     * @param string $message
     */
    protected function log($message=''){
        Logger::log($message);
    }

    /**
     * 开启事务
     */
    protected function startTrans(){
        Db::startTrans();
    }

    /**
     * 提交事务
     */
    protected function commit(){
        Db::commit();
    }


    /**
     * 关闭事务
     */
    protected function rollback(){
        Db::rollback();
    }



}