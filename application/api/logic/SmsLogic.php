<?php

namespace app\api\logic;

use app\common\base\LogicQuery;
use helper\SmsCaptchaHelper;
use think\Request;

class SmsLogic extends LogicQuery
{


    /**
     * 推送验证码
     * @param null $params
     * @return bool
     * @throws \think\Exception
     */
    public function sms($params = null){
        if(empty($params)){
            $params = $this->request->param();
        }
        return SmsCaptchaHelper::send($params['mobile']);
    }

}
