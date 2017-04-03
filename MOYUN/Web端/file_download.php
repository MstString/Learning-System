<?php

/**
 * 下载文件
 * @author string.zhengyang@gmail.com
 * @version 2
 * @package MOYUN
 */
/**
 * 引入用户登陆检测模块(包含全局引用)
 * @since 1
 */
require('logged.php');

/**
 * 引入post类并创建实例
 * @since 1
 */
require(DIR_LIB . DS . 'moyun-post.php');
$oapost = new oapost($db, $ip_arr['id']);

/**
 * 下载文件
 * @since 1
 */
if (isset($_GET['id']) == true) {
    $download_view = $oapost->view($_GET['id']);
    if ($download_view) {
        //判断密码是否匹配
        $download_password_boolean = false;
        if ($download_view['post_password']) {
            if ($_GET['pw'] === $download_view['post_password']) {
                $download_password_boolean = true;
            }
        } else {
            $download_password_boolean = true;
        }
        if ($download_password_boolean == true) {
            $download_parent_view = $oapost->view($download_view['post_parent']);

            $download_dir = substr($download_parent_view['post_date'], 0, 4) . substr($download_parent_view['post_date'], 5, 2) . '/' . substr($download_parent_view['post_date'], 8, 2);
            // plugtourl($website_url . '/' . DIR_DATA . '/files/' . $download_dir . '/' . $download_parent_view['post_name']);
            if (substr(strrchr($download_parent_view['post_name'], '.'), 1) == 'txt' || substr(strrchr($download_parent_view['post_name'], '.'), 1) == 'png' || substr(strrchr($download_parent_view['post_name'], '.'), 1) == 'jpg' || substr(strrchr($download_parent_view['post_name'], '.'), 1) == 'pdf'){
              // $download_path = iconv('UTF-8','GB2312', $website_url . '/' . DIR_DATA . '/files/' . $download_dir . '/' . $download_parent_view['post_name']);
              // header("Content-Type: application/force-download");
              // header("Content-Disposition: attachment; filename=".basename($download_parent_view['post_name']));
              // readfile($download_path);
              // 增加对不同浏览器的支持
                $ua = $_SERVER["HTTP_USER_AGENT"];
                $filename = $download_parent_view['post_name'];
                $encoded_filename = urlencode($filename);
                $encoded_filename = str_replace("+", "%20", $encoded_filename);
                header('Content-Type: application/octet-stream');
                if (preg_match("/MSIE/", $ua)) {
                header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
                readfile($website_url . '/' . DIR_DATA . '/files/' . $download_dir . '/' . $filename);
                } else if (preg_match("/Firefox/", $ua)) {
                header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
                readfile($website_url . '/' . DIR_DATA . '/files/' . $download_dir . '/' . $filename);
                } else {
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                readfile($website_url . '/' . DIR_DATA . '/files/' . $download_dir . '/' . $filename);
                }
            }
            else {
              plugtourl($website_url . '/' . DIR_DATA . '/files/' . $download_dir . '/' . $download_parent_view['post_name']);
            }
        } else {
            plugerror('downloadfile-pw');
        }
    }
}
?>
