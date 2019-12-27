<?php
namespace app\index\controller;

use app\common\base\Controllers;

class Shangping extends Controllers
{   
    
    //操作说明
    public function index()
    {
      return $this->fetch();
    }
	
	# 商品详细
	public function detail()
	{
	  
	    return $this->fetch();
	}
   
	# 购物车
	public function car()
	{
	  
	    return $this->fetch();
	}
	
	# 订单
	public function order()
	{
	  
	    return $this->fetch();
	}
	
	# 提交订单
	public function confirmorder()
	{
	  
	    return $this->fetch();
	}
	
	# 我的
	public function me()
	{
	  
	    return $this->fetch();
	}
}