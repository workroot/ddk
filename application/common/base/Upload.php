<?php
namespace app\common\base;

use think\Exception;
use think\File;
use think\Request;

class Upload extends File
{
    protected static $config;
    protected static $files;

    const DIR_CHMOD = 0755;
    const FILE_CHMOD = 0644;

    const FILE_TYPE_IMAGE = 'image';
    const FILE_TYPE_FLASH = 'flash';
    const FILE_TYPE_MEDIA = 'media';
    const FILE_TYPE_FILE = 'file';

    //后台类目图标存储路径
    const TOUSU_TYPE = 1;
    //文章缩略图目录
    const ARTICLE_TYPE = 2;
    //导入文件目录
    const FILEEXCEL_TYPE = 3;
    //上传logo
    const COMPANY_TYPE = 4;
    //上传二维码
    const CODE_TYPE = 5;

    public static $type = [
        self::TOUSU_TYPE => 'adminCategory',
        self::ARTICLE_TYPE => 'adminArticle',
        self::FILEEXCEL_TYPE => 'excel',
        self::COMPANY_TYPE => 'company',
        self::CODE_TYPE => 'code',
    ];

    /**
     * 上传条件
     * @return array
     */
    protected static function getUploadConfig(){
        if(is_null(self::$config)){
            self::$config = [
                'uri' => IMAGES,
                'root' => sprintf('%s'.IMAGES,realpath(ROOT_PATH)),
                'maxSize' => 50 * 1024 * 1024,
                'exts' => [
                    self::FILE_TYPE_IMAGE => ['gif', 'jpg', 'jpeg', 'png', 'bmp'],
                    self::FILE_TYPE_FLASH => ['swf', 'flv'],
                    self::FILE_TYPE_MEDIA => ['swf', 'flv', 'mp3', 'mp4', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb', 'amr'],
                    self::FILE_TYPE_FILE => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2']
                ],
                'filed'=>'Filedata',
                'water' => '',
            ];
        }
        return self::$config;
    }


    /**
     * 获取上传数据
     * @param null $params
     * @return array|string
     * @throws \Exception
     */
    public static function submit($params = null){
        try{
            if(empty($params)){
                throw new Exception('未获取到上传的参数',1);
            }
            $config = self::getUploadConfig();
            $result = self::receive($config,$params);

        }catch(\Exception $e){
            throw $e;
        }
        return $result;
    }


    /**
     * 获取上传文件处理
     * @param null $config
     * @param $params
     * @return array|string
     * @throws \Exception
     */
    public static function receive($config = null,$params){
        try{
            if(empty($_FILES)){
                throw new Exception('上传为空',1);
            }
            if(is_null($config)){
                $config = self::getUploadConfig();
            }
            self::$files = $config;
            $result = [];
            foreach($_FILES as $field => $file){
                if(!isset($file['name'])){
                    return '非法上传';
                }
                if(is_array($file['name'])){
                    $result[$field] = [];
                    $keys = array_keys($file);
                    foreach($file['name'] as $index=>$name){
                        $item = [];
                        foreach($keys as $key){
                            $item[$key] = $file[$key][$index];
                        }
                        $path = self::upload($item,$params);
                        $result[$field][$index] = self::access($path,$params);
                    }
                }else{
                    $path = self::upload($file,$params);
                    $result[$field] = self::access($path,$params);
                }
            }
        }catch(\Exception $e){
            throw $e;
        }
        return $result;
    }


    /**
     * 将内容写入
     * @param $file
     * @param $params
     * @return array|string
     */
    public static function upload($file,$params){
        self::valida($file);
        $fileExt = substr($file['name'],strripos($file['name'],'.')+1);
        $fileExt = strtolower($fileExt);
        $newFileName = md5(uniqid()) . '.' . $fileExt;
        $savePath = self::getSavePath(self::getFileType($fileExt),$params);
        $filePath = $savePath . '/' . $newFileName;
        //函数将上传的文件移动到新位置
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            return '上传文件失败';
        }
        //改变文件权限
        chmod($filePath, self::FILE_CHMOD);
        $path = str_replace(self::$files['root'], '', $filePath);
        return [
            'path' => $path,
            'name' => $file['name'],
            'size' => filesize($filePath), //函数返回指定文件的大小
        ];
    }


    /**
     * 根据文件后缀获取文件类型
     * @param string $ext
     * @return string
     */
    public static function getFileType($ext) {
        $config = self::getUploadConfig();
        foreach ($config['exts'] as $type => $exts) {
            if (in_array($ext, $exts)) {
                return $type;
            }
        }
        return null;
    }

    /**
     * 创建图片存放目录
     * @param null $type
     * @param null $params
     * @return string
     */
    protected static function getSavePath($type = null,$params = null) {
        if(empty($params)){
            $params = Request::instance()->param();
        }
        $name = isset($params['type'])?self::$type[$params['type']]:'';
        $dir = self::$files['root'];
        if (!empty($type)) {
            $dir = $dir . '/' . $type . '/'. $name ;
            if (!is_dir($dir)) {
                mkdir($dir, self::DIR_CHMOD,true);
            }
        }
        $array = date('Ymd');
        $dir = $dir . '/' . $array;
        if (!is_dir($dir)) {
            mkdir($dir, self::DIR_CHMOD,true);
        }
        return $dir;
    }



    /**
     * 判断是否为一个LIST数组
     * @param array $data
     * @return boolean
     */
    public function isList(&$data) {
        if (empty($data) || !is_array($data)) {
            return false;
        }
        $keys = array_keys($data);
        $values = array_values($data);
        return ($keys === array_keys($values));
    }


    /**
     * 返回数据
     * @param $file
     * @return array|null
     */
    protected static function access($file) {
        if (empty($file)) {
            return null;
        }
        return [
            'name' => $file['name'],
            'path' => $file['path'],
            'url' => self::$files['uri'] . $file['path'],
            'size' => $file['size'],
        ];
    }










    /**
     * 判断上传的格式是否正确
     * @param $file
     * @return string
     */
    protected static function valida($file) {
        $fileName = $file['name'];
        $tmpName = $file['tmp_name'];
        $fileSize = $file['size'];

        if (empty($fileName)) {
            return '请选择文件';
        }

        $config = self::$files;
        $savePath = $config['root'];

        if (!is_dir($savePath)) {
            return '上传目录不存在';
        }

        if (!is_writable($savePath)) {
            return '上传目录没有写权限';
        }

        if ($fileSize > $config['maxSize']) {
            return '上传文件大小超过限制';
        }

        $array = explode('.', $fileName);
        $fileExt = array_pop($array);
        $fileExt = trim($fileExt);
        $fileExt = strtolower($fileExt);
        $valid = false;
        foreach ($config['exts'] as $exts) {
            if (in_array($fileExt, $exts)) {
                $valid = true;
                break;
            }
        }
        if (!$valid) {
            return '文件扩展名是不允许的扩展名';
        }
    }
}