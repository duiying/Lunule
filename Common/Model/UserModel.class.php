<?php

/**
 * 用户模型类
 */

class UserModel extends Model
{
	// 表名
	public $table = 'user';

	/**
	 * 检查用户名和密码是否正确
	 * @param string $username 用户名
	 * @param string $password 密码
	 * @return array $userInfo 用户信息
	 */
	public function validate($username, $password) {
		if (!$username) return false;
		$userInfo = $this->where(['username' => $username])->find();
		
		// 用户名不存在
		if (!$userInfo) return false;

		// 密码错误
		if ($userInfo['password'] != md5($password)) return false;

		return $userInfo;
	}

}