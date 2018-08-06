<?php

/**
 * 后台首页控制器
 */

class IndexController extends CommonController
{
	public function index() {
		$this->display();
	}

	public function info() {
		$basicInfo = basicInfo();
		$this->assign('basicInfo', $basicInfo);
		$this->display();
	}
}	