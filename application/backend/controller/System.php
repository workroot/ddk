<?php
namespace app\backend\controller;

use app\backend\base\AuthController;
use helper\Assistant;
use think\Cache;
use think\Db;

/**
 * 系统配置
 * Class System
 * @package app\admin\controller
 */
class System extends AuthController
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 站点配置
     */
    public function siteConfig()
    {
        $site_config = Db::name('system')->field('value')->where('name', 'site_config')->find();
        $site_config = unserialize($site_config['value']);

        return $this->fetch('site_config', ['site_config' => $site_config]);
    }

    /**
     * 更新配置
     */
    public function updateSiteConfig()
    {
        if ($this->request->isPost()) {
            $site_config                = $this->request->post('site_config/a');
            $site_config['site_tongji'] = htmlspecialchars_decode($site_config['site_tongji']);
            $data['value']              = serialize($site_config);
            if (Db::name('system')->where('name', 'site_config')->update($data) !== false) {
                $this->success('提交成功');
            } else {
                $this->error('提交失败');
            }
        }
    }

    /**
     * 清除缓存
     */
    public function clear()
    {
        if (Assistant::delete_dir_file(CACHE_PATH) || Assistant::delete_dir_file(TEMP_PATH)) {
            $this->success('清除缓存成功');
        } else {
            $this->error('清除缓存失败');
        }
    }
}