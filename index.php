<?php  

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.4.0','<'))  die('require PHP > 5.4.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为FALSE
define('DEBUG', TRUE);

// 定义应用目录
define('APP_NAME', 'Index');

// 引入Lunule入口文件
require './Lunule/Lunule.php';