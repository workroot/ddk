<?php

namespace app\calculator\base;

use think\Controller;

class Controllers extends Controller{

    /**
     * 成功返回JSON信息
     * @param array $data
     * @return mixed
     */
    public function renderSuccess($msg = '',$data = []){
        if(is_array($msg)){
            return $this->renderJson([STATUS_SUCCESS],$msg);
        }else{
            return $this->renderJson(empty($msg)?[STATUS_SUCCESS]:[STATUS_SUCCESS,$msg],$data);
        }
    }



    /**
     * 返回失败的JSON
     * @param string $msg
     * @return string
     */
    protected function renderFailureJson($msg = null) {
        $error = [STATUS_FAILURE, empty($msg) ? Mapper::$ERROR_DESC[STATUS_FAILURE] : $msg];
        return $this->renderJson($error);
    }


    /**
     * 错误数据
     * @param $e
     * @return string
     */
    protected function renderError($e){
        if(is_object($e)){
            $error = [STATUS_FAILURE, '系统错误'];
            $data = [];
            if ($e instanceof \Exception) {
                $error[1] = $e->getMessage();
            }
            if (config('app_debug') === true) {
                $data['status'] = $e->getCode();
                $data['message'] = $e->getMessage();
                $data['trace'] = $e->getTraceAsString();
            }
        }else{
            $error = [STATUS_FAILURE,$e];
            $data = [];
        }
        return $this->renderJson($error,$data);
    }


    /**
     * 页面跳转提示
     * @param $message
     */
    protected function html_404($message){
        if(is_array($message)){
            return $message;
        }else{
            echo "<script src='/public/js/jquery.min.js'></script><script src='/public/layer/layer.js'></script><script>layer.msg($message.message,{time:5000},function(){ window.location.href = 'javascript:history.go(-1)';}); </script> ";
        }
    }



    /**
     * 返回JSON
     * @param array $error
     * @param array $data
     * @return string
     */
    protected function renderJson($error, $data = []) {
        $result = [
            'code' => $error[0],
            'message' => isset($error[1]) ? $error[1] : (isset(Mapper::$ERROR_DESC[$error[0]]) ? Mapper::$ERROR_DESC[$error[0]] : null),
            'data' => $data,
        ];
        return $this->renderSimpleJson($result);
    }


    /**
     * 返回原JSON字符串
     * @param mixed $data
     * @return string
     */
    protected function renderSimpleJson($data = null) {
        if (isset($_SERVER['HTTP_ACCEPT']) && (stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            return $data;
        }
        return json_encode($data);
    }



    /**
     * 添加错误日志
     * @param string $message
     */
    protected function log($message=''){
        Logger::log($message);
    }

}