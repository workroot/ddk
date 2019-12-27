<?php

namespace app\index\controller;

use app\common\base\Controllers;


use app\common\model\Qq;
use PHPMailer\PHPMailer\PHPMailer;
use think\Db;
use think\Loader;

Loader::import('PHPMailer.src.Exception');
Loader::import('PHPMailer.src.PHPMailer');
Loader::import('PHPMailer.src.SMTP');

class Email extends Controllers
{

    /**
     * 系统邮件发送函数
     * @param string $tomail 接收邮件者邮箱
     * @param string $name 接收邮件者名称
     * @param string $subject 邮件主题
     * @param string $body 邮件内容
     * @param string $attachment 附件列表
     * @return boolean
     * @author static7 <static7@qq.com>
     */
    public function send_mail($tomail, $name, $subject = '', $body = '', $attachment = null)
    {
        $mail = new PHPMailer();           //实例化PHPMailer对象
        $mail->CharSet = 'UTF-8';           //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $mail->IsSMTP();                    // 设定使用SMTP服务
        $mail->SMTPDebug = false;               // SMTP调试功能 0=关闭 1 = 错误和消息 2 = 消息
        $mail->SMTPAuth = true;             // 启用 SMTP 验证功能
        $mail->SMTPSecure = 'ssl';          // 使用安全协议
        $mail->Host = " smtp.163.com"; // SMTP 服务器
        $mail->Port = 465;                  // SMTP服务器的端口号
        $mail->Hostname = 'localhost';
        $mail->Username = "choeon_yson@163.com";    // SMTP服务器用户名
        $mail->Password = "iaw8023";     // SMTP服务器密码
        $mail->SetFrom('choeon_yson@163.com', $name);
        $replyEmail = '';                   //留空则为发件人EMAIL
        $replyName = '';                    //回复名称（留空则为发件人名称）
        $mail->AddReplyTo($replyEmail, $replyName);
        $mail->Subject = $subject;
        $mail->MsgHTML($body);
        $mail->AddAddress($tomail, $name);
        return $mail->Send() ? 1 : $mail->ErrorInfo;
    }



    /**
     * tp5邮件
     * @param
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */
    public function email()
    {
        $QQ = Db::name('Qq')->where('status' ,'=',0)->limit(100)->select();
        foreach($QQ as $item){
            $toemail = $item['QQ'].'@qq.com';
            $name = '大数据';
            $subject = '来查信，查尽一切！！！！';
            $mea = '有预期？贷款申请一直被拒？是否为网袋黑名单？多头借贷有多严重？还有多少贷款没有还？社交风险到底高不高个人信用评分及信贷通过率？历史案件是否结案？身份证是否被他人利用等等....快来查一查信用即可一目了然、点击下方链接查询：';
            $content = $mea .'【<a href="http://jrr9.cn/308a0" rel="noopener" target="_blank">大数据</a>】或扫描下方二维码：</br><a href="http://i2.tiimg.com/700865/6b0f3deb73019310.jpg" rel="noopener" target="_blank"><img src="http://i2.tiimg.com/700865/6b0f3deb73019310.jpg" width="160" height="300"></a>';
            $result = $this->send_mail($toemail, $name, $subject, $content);
            if($result == 1){
                db('Qq')->where('id', $item['id'])->update(['status' => '1']);
                dump($result);
            }else{
                dump($result);
                db('Qq')->where('id', $item['id'])->update(['status' => '2']);
            }
        }
    }

    /**
     * 生成QQ
     * @param int $quantity
     */
    public function index($quantity = 10000)
    {
        $st = microtime(true);
        for ($i = 0; $i < $quantity; $i++) {
            $str = mt_rand(10000000, 99999999);
            $q = $this->qq($str);
            if (empty($q)) {
                continue;
            }
        }
        echo microtime(true) - $st;
    }


    /**
     * QQ加入表
     * @param $qq
     * @return false|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function qq($qq)
    {
        $QQ = Db::name('Qq')->where('QQ', '=', $qq)->find();
        if (empty($QQ)) {
            $data = [
                'QQ' => $qq,
                'status' => 0,
                'createAt' => time()
            ];
            $query = new Qq();
            $q = $query->allowField(true)->save($data);
            return $q;
        }
    }

}