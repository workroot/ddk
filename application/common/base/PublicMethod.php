<?php

namespace app\common\base;


use think\Controller;

class PublicMethod extends Controller{


    /**
     * 加密处理
     * @param string $params
     * @return string
     */
    public static function encryption($params = ''){
            if(isset($params) && !empty($params) && is_numeric($params)){
                 return base64_encode(($params * Mapper::PARAM_NUM));
            }else{
                 return base64_encode(base64_encode($params));
            }
    }


    /**
     * 解密处理
     * @param string $params
     * @return bool|string
     */
    public static function decrypt($params = ''){
        $par = base64_decode($params)/Mapper::PARAM_NUM;
        if(isset($par) && !empty($par) && is_numeric($par)){
            return $par;
        }else{
            return base64_decode(base64_decode($params));
        }
    }

}