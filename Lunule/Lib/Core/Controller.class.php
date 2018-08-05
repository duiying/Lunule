<?php

/**
 * 基类Controller
 */

class Controller extends SmartyView
{

	private $var = [];

	public function __construct() {
		if(config('SMARTY_ON')) parent::__construct();

		// 框架初始化方法 __init & __auto
		if (method_exists($this, '__init')) {
			$this->__init();
		}
		if (method_exists($this, '__auto')) {
			$this->__init();
		}
	}

	/**
	 * 成功提示方法
	 * @param string $msg 提示信息
	 * @param string $url 跳转地址
	 * @param integer $time 等待时间
	 */
	protected function success($msg, $url = NULL, $time = 3) {
		$url = $url ? "window.location.href = '" . $url . "'" : 'window.history.back(-1)';
		include APP_TPL_PATH . '/success.html';
		die;
	}

	/**
	 * 失败提示方法
	 * @param string $msg 提示信息
	 * @param string $url 跳转地址
	 * @param integer $time 等待时间
	 */
	protected function error($msg, $url = NULL, $time = 3) {
		$url = $url ? "window.location.href = '" . $url . "'" : 'window.history.back(-1)';
		include APP_TPL_PATH . '/error.html';
		die;
	}

	protected function get_tpl($tpl) {
		if (is_null($tpl)) {
			$path = APP_TPL_PATH . '/' . CONTROLLER . '/' . ACTION . '.html';
		} else {
			$suffix = strrchr($tpl, '.');
			$tpl = empty($suffix) ? $tpl . '.html' : $tpl;
			$path = APP_TPL_PATH . '/' . CONTROLLER . '/' . $tpl;
		}

		return $path;
	}

	/**
	 * 载入模板
	 * @param string $tpl 模板文件名
	 */
	protected function display($tpl = NULL) {
		$path = $this->get_tpl($tpl);

		if (!is_file($path)) halt($path . '模板文件不存在');

		if(config('SMARTY_ON')) {
			parent::display($path);
		} else {
			extract($this->var);
			include $path;
		}
	}

	/**
	 * 赋值
	 * @param string $var 变量名
	 * @param string $value 变量值
	 */
	protected function assign($var, $value) {
		if(config('SMARTY_ON')) {
			parent::assign($var, $value);
		} else {
			$this->var[$var] = $value;
		}
	}
}

?>