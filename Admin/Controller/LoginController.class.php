<?php

/**
 * 登录控制器
 */

class LoginController extends Controller 
{

	/**
	 * 显示登录页面
	 */
	public function index() {
		$this->display();
	}

	/**
	 * 处理登录操作
	 */
	public function check() {
		if ($userInfo = ext_model('User')->validate($_POST['username'], $_POST['password'])) {
			$_SESSION['uid'] = $userInfo['uid'];
			$_SESSION['username'] = $userInfo['username'];
			return ajaxReturn('200', '登录成功');
		}

		return ajaxReturn('201', '用户名或密码错误');
	}

	/**
	 * 退出登录
	 */
	public function logout() {
		session_unset();
		session_destroy();

		return ajaxReturn('200', '退出成功');
	}
}