<?php

namespace app\backend\controller;

use app\backend\base\AuthController;
use app\backend\logic\CompanyLogicQuery;
use app\backend\logic\ExcelLogicQuery;
use app\backend\logic\UploadLogicQuery;
use app\common\model\Company as CompanyModel;
use app\common\base\Mapper;

class Company extends AuthController{

    protected $company_model;

    protected function _initialize()
    {
        parent::_initialize();
        $this->company_model = new CompanyModel();
    }

    /**
     * 列表
     * @param string $keyword
     * @param string $start_time
     * @param string $end_time
     * @param string $platformType
     * @return mixed|string
     */
    public function index($keyword = '',$start_time='',$end_time = '',$platformType = ''){
        try{
            $post = $this->request->param();
            $data = CompanyLogicQuery::getInstance()->findAll($post);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        $this->assign('data',$data);
        $this->assign('start_time',$start_time);
        $this->assign('end_time',$end_time);
        $this->assign('keyword',$keyword);
        $loan = Mapper::$LOAN;
        $this->assign('loan',$loan);
        $status = Mapper::$COMPANY_STATUS;
        $this->assign('status',$status);
        $this->assign('sts',$platformType);
        return $this->fetch();
    }


    /**
     * 添加页面
     * @return mixed
     */
    public function add(){
        $get = $this->request->param();
        $loan = Mapper::$LOAN;
        $this->assign('loan',$loan);
        $age = Mapper::$AGE;
        $this->assign('age',$age);
        $status = Mapper::$COMPANY_STATUS;
        $this->assign('status',$status);
        $iswhether = Mapper::$ISWHETHER;
        $this->assign('iswhether',$iswhether);
        $islcense = Mapper::$ISLICENSE;
        $this->assign('islcense',$islcense);
        $this->assign('get',$get);
        return $this->fetch();
    }


    /**
     * 数据提交
     * @return mixed|string
     */
    public function save(){
        if ($this->request->isPost()) {
            try{
                $data = $this->request->param();
                $status = CompanyLogicQuery::getInstance()->save($data,$this->company_model);
            }catch(\Exception $e){
                return $this->renderError($e);
            }
            return $this->renderSuccess($status,['redirect'=>'/backend/company/index']);
        }
    }

    /**
     * 数据更新
     * @return mixed|string
     */
    public function update()
    {
        if ($this->request->isPost()) {
            try{
                $data = $this->request->param();
                $status = CompanyLogicQuery::getInstance()->update($data,$this->company_model);
            }catch(\Exception $e){
                return $this->renderError($e);
            }
            return $this->renderSuccess($status,['redirect'=>'/backend/company/index']);
        }
    }

    /**
     * 编辑菜单
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $company = $this->company_model->find($id);
        $loan = Mapper::$LOAN;
        $this->assign('loan',$loan);
        $age = Mapper::$AGE;
        $this->assign('age',$age);
        $status = Mapper::$COMPANY_STATUS;
        $this->assign('status',$status);
        $iswhether = Mapper::$ISWHETHER;
        $this->assign('iswhether',$iswhether);
        $islcense = Mapper::$ISLICENSE;
        $this->assign('islcense',$islcense);
        return $this->fetch('edit', ['company' => $company]);
    }


    /**
     * 删除菜单
     * @param $id
     */
    public function delete($id)
    {
        if ($this->company_model->destroy($id)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }


    /**
     * 导出模板
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public function excel(){
         ExcelLogicQuery::getInstance()->excel();
    }

    /**
     * 导入数据库
     * @return mixed
     */
    public function imports(){
        try{
            $data = $this->request->post();
            $file = UploadLogicQuery::getInstance()->single($data);
            $status = ExcelLogicQuery::getInstance()->inserExcel($file['file']['url']);
        }catch(\Exception $e){
            return $this->renderError($e);
        }
        return $this->renderSuccess($status,['redirect'=>'/backend/company/index']);
    }
}