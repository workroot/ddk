<?php

namespace app\calculator\base;

class Mapper{


    //参数加密控制
    const PARAM_NUM = 100;


    //产品类型
    const PRODUCT_TYPE = 1 ; //平台
    const PRODUCT_TYPE_TWO = 2 ; //律师咨询


    //平台设置测试查询价格指定ID
    public static $TEST_ID  = ['1','2','501462'];


    //普通用户注册默认次数
    const SHARE_USER = 1;



    //默认用户查询指定价格PID

    const PID_PINGTTAI_ID = 1;
    const PID_ZIXUN_ID = 2;



    //微信支付商品名称

    const WX_PROEUCT_NAME_ONE = '平台信息';

    const WX_PROEUCT_NAME_TWO = '律师咨询';

    public static $WX_PRODUCE_NAME = [
        self::PRODUCT_TYPE => '平台信息',
        self::PRODUCT_TYPE_TWO => '律师咨询',
    ];


    //格式
    const FORMAT_RAW = 'raw';
    const FORMAT_HTML = 'html';
    const FORMAT_JSON = 'json';
    const FORMAT_JSONP = 'jsonp';
    const FORMAT_XML = 'xml';




    public static $ERROR_NO = [
        'SUCCESS' => STATUS_SUCCESS,
        'FAILURE' => STATUS_FAILURE,
    ];

    public static $ERROR_DESC = [
        STATUS_FAILURE => '失败',
        STATUS_SUCCESS => '成功',
    ];


    //文章对应对象
    const ARTICLE_ALL = 0;
    const ARTICLE_USER = 2;
    const ARTICLE_AGENT = 3;

    public static $ARTICLE_TYPE = [
        self::ARTICLE_ALL => '全部',
        self::ARTICLE_USER => '用户',
        self::ARTICLE_AGENT => '代理',
    ];


    //公告对应对象 ANNOUNCEMENT
    const ANNOUNCEMENT_ALL = 0;
    const ANNOUNCEMENT_USER = 2;
    const ANNOUNCEMENT_AGENT = 3;
    const ANNOUNCEMENT_PERSONAL = 4;

    public static $ANNOUNCEMENT = [
        self::ANNOUNCEMENT_ALL => '全部',
        self::ANNOUNCEMENT_USER => '用户',
        self::ANNOUNCEMENT_AGENT => '代理',
        self::ANNOUNCEMENT_PERSONAL => '个人',
    ];


    //log 和 轮播图
    const CAROUSEL_IMAGE = 1;
    const LOG_IMAGE = 2;
    public static $CAROUSEL_TYPE = [
        self::CAROUSEL_IMAGE => '轮播',
        self::LOG_IMAGE => 'LOGO',
    ];


    /**
     *  公司数据填充
     */

    //贷款类型
    const LOAN_ZERO = 0;
    const LOAN_ONE = 1;
    const LOAN_TWO = 2 ;
    const LOAN_THREE = 3;
    const LOAN_FOUR = 4;
    public static $LOAN = [
        self::LOAN_ZERO => '未知',
        self::LOAN_ONE => '小额现金贷',
        self::LOAN_TWO => '消费贷款',
        self::LOAN_THREE => 'p2p网贷',
        self::LOAN_FOUR => '销售贷款',
    ];

    public static $LOAN_NUMBER = [
        '未知' => self::LOAN_ZERO ,
        '小额现金贷' => self::LOAN_ONE ,
        '消费贷款' => self::LOAN_TWO ,
        'p2p网贷' => self::LOAN_THREE ,
        '销售贷款' =>  self::LOAN_FOUR ,
        ];

    //提交状态
    const STATUS_ZERO = 0;
    const STATUS_ONE = 1;
    const STATUS_TWO = 2 ;
    const STATUS_THREE = 3;
    public static $COMPANY_STATUS = [
        self::STATUS_ZERO => '已驳回',
        self::STATUS_ONE => '待审核',
        self::STATUS_TWO => '已通过',
    ];

    //年龄
    const AGE_ZERO = 0;
    const AGE_ONE = 1;
    const AGE_TWO = 2 ;
    const AGE_THREE = 3;
    const AGE_FOUR = 4;
    public static $AGE = [
        self::AGE_ZERO => '18~25',
        self::AGE_ONE => '25~35',
        self::AGE_TWO => '35~45',
        self::AGE_THREE => '45~55',
        self::AGE_FOUR => '55以上',
    ];

    //是否
    const ISWHETHER_ZERO = 0;
    const ISWHETHER_ONE = 1;

    public static $ISWHETHER = [
        self::ISWHETHER_ZERO => '否',
        self::ISWHETHER_ONE => '是',
    ];

    public static $IS_NUMBER = [
          '否' => self::ISWHETHER_ZERO,
          '是' => self::ISWHETHER_ONE,
    ];

    //持有金融牌照类型
    const ISLICENSE_ZERO = 0;
    const ISLICENSE_ONE = 1;
    const ISLICENSE_TWO = 2 ;
    public static $ISLICENSE = [
        self::ISLICENSE_ZERO => '未知',
        self::ISLICENSE_ONE => '未知',
        self::ISLICENSE_TWO => '未知',
    ];

    public static $ISLICENSE_NUMBER = [
         '是' => self::ISLICENSE_ZERO ,
         '未知' => self::ISLICENSE_ONE ,
         '否' => self::ISLICENSE_TWO ,
    ];




    //订单类型；
    const PRICE_ONE = 1; //懂贷咖
    const PRICE_TWO = 2 ;
    public static $PRICE_STATIC = [
        self::PRICE_ONE => '',
        self::PRICE_TWO => '未知',
    ];


    const AMOUNT_FOUR = 4;
    const AMOUNT_FIVES = 5;
    const AMOUNT_SIX = 6;
    const AMOUNT_SEVEN = 7;
    const AMOUNT_EIGHT = 8;
    const AMOUNT_NINE = 9;
    const AMOUNT_TEN = 10;
    const AMOUNT_ELEVEN = 11;
    const AMOUNT_TWELVE = 12;
    const AMOUNT_THIRTEEN = 13;




}
