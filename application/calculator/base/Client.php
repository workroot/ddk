<?php
namespace app\calculator\base;

use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Core\Config;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Core\Profile\DefaultProfile;

/**
 * 阿里云短信客户端
 * @author wansong.ge
 *
 */
class Client {
	protected static $instance;


    protected static $_config = [
        'accessKeyId' => 'LTAI69xmuoeY8t82',
        'accessKeySecret' => 'E0HA6PLdwdepY7kPRVEZm4NyCtfVWX',
        'signName' => '钻石好信',
        'templateCode' => 'SMS_159782289',
    ];

	/**
	 * 
	 * @var DefaultAcsClient
	 */
	protected $acsClient;
	
	/**
	 * 单例获取
	 * @return static|self|Client
	 */
	public static function getInstance() {
		if (is_null(static::$instance)) {
			static::$instance = new static();
		}
		return static::$instance;
	}
	
	public function __construct() {
		// 加载区域结点配置
		Config::load();
		
		// 短信API产品名
		$product = "Dysmsapi";//SendSms
		
		// 短信API产品域名
		$domain = "dysmsapi.aliyuncs.com";
		
		// 暂时不支持多Region
		$region = "cn-hangzhou";
		
		// 服务结点
		$endPointName = "cn-hangzhou";
		
		// 初始化用户Profile实例
		$config = static::getConfig();



		$profile = DefaultProfile::getProfile($region, $config['accessKeyId'], $config['accessKeySecret']);
		// 增加服务结点
		DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);
		// 初始化AcsClient用于发起请求
		$this->acsClient = new DefaultAcsClient($profile);
	}

    /**
     * 发送短信验证码
     * @param $mobile
     * @param $params
     * @param null $outId
     * @return mixed|\SimpleXMLElement
     * @throws \Exception
     */
	public function sendCaptcha($mobile, $params, $outId = null) {
		if (!isset($params['code'])) {
			throw new \Exception('缺少变量(code)', 500);
		}
		
		$config = static::getConfig();
		// 初始化SendSmsRequest实例用于设置发送短信的参数
		$request = new SendSmsRequest();
		
		// 必填，设置雉短信接收号码
		$request->setPhoneNumbers($mobile);

		// 必填，设置签名名称
		$request->setSignName($config['signName']);
		
		// 必填，设置模板CODE
		$request->setTemplateCode($config['templateCode']);
		
		// 可选，设置模板参数
		$request->setTemplateParam(json_encode($params));
		
		// 可选，设置流水号
		if ($outId) {
			$request->setOutId($outId);
		}

		// 发起访问请求
		$acsResponse = $this->acsClient->getAcsResponse($request);
		return $acsResponse;
	}
	
	

	
	/**
	 * 获取配置
	 * @return mixed
	 */
	protected static function getConfig() {
		if (is_null(static::$_config)) {
			static::$_config ;
		}
		return static::$_config;
	}
	
}