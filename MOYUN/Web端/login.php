<?php

/**
 * 登陆处理
 * @author string.zhengyang@gmail.com
 * @version 2
 * @package MOYUN
 */
/**
 * 引入全局定义
 */
 // 方便于对路径的控制
require('glob.php');

/**
 * 引入用户操作封装
 */
require(DIR_LIB . DS . 'moyun-user.php');

/**
 * 检查变量存在并转移给user类
 */
 // isset函数检查变量是否存在 empty函数检查变量的值是否为空
if (empty($_POST['user']) == false && empty($_POST['pass']) == false && empty($_POST['vcode']) == false) {
    if ($_POST['vcode'] == $_SESSION['vcode']) {
        $remember = false;
        if (isset($_POST['remeber']) == true) {
            $remember = true;
        }
        $user = new moyunuser($db);
        $login_bool = $user->login($_POST['user'], $_POST['pass'], $ip_arr['id'], $remember);
        if ($login_bool == true) {
            plugtourl('init.php');
        } else {
            plugtourl('error.php?e=login');
        }
    } else {
        plugtourl('error.php?e=login-vcode');
    }
} else {
        plugtourl('index.php');
}

?>
