# Lunule
一款基于MVC模式并集成了Smarty3模板引擎的PHP框架,并基于该框架实现一个简易的带后台登录以及curd操作的管理系统

### 前言
```
市面上已经有成熟的PHP框架,比如Yii,Laravel,ThinkPHP,Yaf,CI...,我们为什么还要设计自己的PHP框架呢?  
这是因为在设计自己框架的过程中,我们会更加深入地了解框架的运行原理,培养自己面向对象的编程思维,同时也会提升自身的编码能力
```
### 环境要求
```
PHP版本>=5.4
```
### 目录结构
```
www WEB部署目录
|--index.php    前台应用模块入口文件
|--admin.php    后台应用模块入口文件
|--README.md    README文件
|--Admin        后台应用模块目录
|--Index        前台应用模块目录(首次运行项目时会自动生成)
|--Common       公共目录
|--Lunule       框架目录
|--Static       静态资源目录
|--Temp         临时目录
```
其中框架目录Lunule的目录结构为:
```
Lunule 框架目录
|--Config         框架核心配置目录
|   |--config.php 框架核心配置文件
|--Data           框架资源目录
|   |--Tpl        框架模板目录
|--Extends        框架扩展类库目录
|   |--Lib        框架扩展类库目录
|   |--Tool       框架工具类库目录
|--Lib            框架库文件目录
|   |--Core       框架核心库文件目录
|   |--Function   框架核心函数目录
|--Lunule.php     框架入口文件
```
### 入口文件
在项目根目录下,有入口文件index.php和admin.php,index.php负责Index模块的入口,admin.php负责Admin模块的入口  
比如index.php的文件内容为:
```
<?php  

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.4.0','<'))  die('require PHP > 5.4.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为FALSE
define('DEBUG', TRUE);

// 定义应用目录
define('APP_NAME', 'Index');

// 引入Lunule入口文件
require './Lunule/Lunule.php';
```

