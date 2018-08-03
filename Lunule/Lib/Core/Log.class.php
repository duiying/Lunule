<?php

/**
 * 日志处理
 */

class Log
{
	
	/**
	 * 写日志
	 * @param string $msg 错误信息
	 * @param string $level 错误级别
	 * @param integer $type 设置错误信息应该发送到何处 3表示错误信息被发送到$dest的文件里
	 * @param string $dest 目标
	 */
	public static function write($msg, $level = 'ERROR', $type = 3, $dest = NULL) {
		if (!config('SAVE_LOG')) return;
		if (is_null($dest)) {
			$dest = LOG_PATH . '/' . date('Y_m_d') . '.log';
		}
		if (is_dir(LOG_PATH)) {
			error_log('[TIME]: ' . date('Y-m-d H:i:s') . " {$level}: {$msg}" . PHP_EOL,  $type, $dest);
		}
	}

}

?>