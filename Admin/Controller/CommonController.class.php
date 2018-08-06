<?php  

/**
 * 公共控制器
 */

class CommonController extends Controller
{
	/**
	 * 检查是否登录
	 */
	public function __init() {
		if (!isset($_SESSION['uid']) || !isset($_SESSION['username'])) {
			go(__APP__ . '?c=Login');
		}
	}
}