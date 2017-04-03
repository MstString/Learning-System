<?php

/**
 * 已登陆检测
 * <p>如果发现尚未登陆，则直接中断页面</p>
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
 * 进行登陆检测
 * @since 2
 */
//读取用户超时配置
$config_user_timeout = (int) $oaconfig->load('USER_TIMEOUT');
$moyunuser = new moyunuser($db);
$logged_admin = false;
//添加一个教师用户权限组
$logged_teacher = false;
//添加各个年级的分组
$logged_2014 = false;
$logged_2015 = false;
$logged_2016 = false;
if ($moyunuser->status($ip_arr['id'], $config_user_timeout) == true) {
    $logged_user = $moyunuser->view_user($moyunuser->get_session_login());
    if ($logged_user) {
        $logged_group = $moyunuser->view_group($logged_user['user_group']);
        if ($logged_group) {
            if ($logged_group['group_power'] == 'admin') {
                $logged_admin = true;
            }
            else if ($logged_group['group_power'] == 'teacher'){
                $logged_teacher = true;
            }
            else if ($logged_group['group_power'] == '2014') {
                $logged_2014 = true;
            }
            else if ($logged_group['group_power'] == '2015') {
                $logged_2015 = true;
            }
            else if ($logged_group['group_power'] == '2016') {
                $logged_2016 = true;
            }
        }
    }
} else {
    //如果尚未登陆处理
    plugerror('logged');
}
unset($config_user_timeout);

/**
 * 判断网站开关且是否为管理员
 * @since 3
 */
$website_on = $oaconfig->load('WEB_ON');
if (!$website_on && !$logged_admin) {
    plugerror('webclose');
}
?>
