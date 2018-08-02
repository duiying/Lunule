<?php

class Controller 
{
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
}