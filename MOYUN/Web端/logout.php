<?php

/**
 * 退出登陆操作
 * @author string.zhengyang@gmail.com
 * @version 2
 * @package MOYUN
 */
/**
 * 引入全局
 * @since 1
 */
require('glob.php');

/**
 * 引入用户类
 * @since 1
 */
require(DIR_LIB . DS . 'moyun-user.php');

/**
 * 进行退出登陆操作
 */
$moyunuser = new moyunuser($db);
$moyunuser->logout($ip_arr['id']);
plugtourl('index.php');
?>
