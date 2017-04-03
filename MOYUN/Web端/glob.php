<?php

/**
 * 全局设定
 * @author string.zhengyang@gmail.com
 * @version 2
 * @package MOYUNCore
 */
/**
 * 相对路径定义
 */
define('DS', '/');
define('DIR_LIB', 'includes');
define('DIR_DATA', 'content');

/**
 * 设定时区
 */
date_default_timezone_set('PRC');

/**
 * 开启会话
 */
@session_start();

/**
 * 网站测试开关
 */
define('SYS_DEBUG', true);

/**
 * 引入数据库定义
 */
require(DIR_DATA . DS . 'configs' . DS . 'db.inc.php');

/**
 * 跳转模块
 */
require(DIR_LIB . DS . 'plug-tourl.php');

/**
 * 引入错误处理模块
 */
require(DIR_LIB . DS . 'core-error.php');
require(DIR_LIB . DS . 'plug-error.php');

/**
 * 引入并初始化数据库连接<br/>
 * 保留$db变量用于后面使用
 */
require(DIR_LIB . DS . 'core-db.php');
$db = new coredb($db_dns, $db_username, $db_password, $db_persistent);
$db->set_encoding($db_encoding);

/**
 * 初始化配置操作句柄
 */
require(DIR_LIB . DS . 'moyun-configs.php');
$oaconfig = new oaconfigs($db);

/**
 * 初始化IP地址
 */
require(DIR_LIB . DS . 'core-ip.php');
$coreip = new coreip(DIR_DATA . DS . 'configs' . DS . 'qqwry.dat', $db);
$ip_arr = $coreip->get_ip();

/**
 * 初始化日志操作
 */
require(DIR_LIB . DS . 'core-log.php');
$log = new corelog($ip_arr['addr'], $db, true);

/**
 * 获取页面基本配置内容
 */
//网站标题
$website_title = $oaconfig->load('WEB_TITLE');
//页脚信息
$website_footer = $oaconfig->load('PAGE_FOOTER_COPYRIGHT');

/**
 * 上传文件存储路径
 */
define('UPLOADFILE_DIR', DIR_DATA . DS . 'files');

/**
 * 上传作业存储路径
 */
define('UPLOADFILE_DIR_HOMEWORK', DIR_DATA . DS . 'homework');

/**
 * 获取网站URL
 */
$website_url = $oaconfig->load('WEB_URL');
?>
