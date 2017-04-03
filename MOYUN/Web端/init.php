<?php
/**
* 登录后首页
* @author string.zhengyang@gmail.com
* @version 2
* @package MOYUN
*/

/**
* 引入用户登陆检测模块(包含全局引用)
*/
require('logged.php');

/**
* 获取当前页面URL
*/
require(DIR_LIB . DS . 'plug-geturl.php');
$url = pluggeturl();

/**
* 定义页面指向
*/
$init_page = 0;
if (isset($_GET['init']) == true) {
  $init_page = $_GET['init'];
  if($init_page > 11){
    if($logged_admin == false){
      plugerror('noadmin');
    }
  }
}
// 从0开始计算
$init_page_arr = array('center', 'message', 'disk_user', 'task_user', 'performance', 'note', 'address_book', 'self', 'disk_share', 'task_center', 'schedule', 'message_board', 'message_center', 'system', 'backup', 'user', 'user_group');
if (isset($init_page_arr[$init_page]) == false) {
  $init_page = 0;
}

/**
* 初始化页面URL
*/
$page_url = 'init.php?init=' . $init_page;

/**
* 当前用户ID
*/
$post_user = $moyunuser->get_session_login();

/**
* 引入post类并创建实例
*/
require(DIR_LIB . DS . 'moyun-post.php');
$oapost = new oapost($db, $ip_arr['id']);

/**
* 计算用户消息提示
*/
$tip_message_row = $oapost->view_list_row(null, null, null, 'private', 'message', null, $post_user);

/**
* 计算可接收计划的个数
*/
$tip_task_center_row = $oapost->view_list_row(null, null, null, 'public', 'task', 0);

/**
* 计算正在进行的计划的个数
*/
$tip_task_user_row = $oapost->view_list_row($post_user, null, null, 'public', 'task', '');

/**
* 自动备份
*/
$config_backup_auto_on = $oaconfig->load('BACKUP_AUTO_ON');
$config_backup_date = '';
if ($config_backup_auto_on && isset($_GET['auto']) == false && isset($_SESSION['backup-auto']) == false) {
  $config_backup_date = $oaconfig->load('BACKUP_LAST_DATE');
  $config_backup_date_time = mktime(0, 0, 0, (int) substr($config_backup_date, 4, 2), (int) substr($config_backup_date, 6, 2), (int) substr($config_backup_date, 0, 4));
  $backup_auto_cycle = (int) ((int) time() - $config_backup_date_time) / 86400;
  $config_backup_auto_cycle = (int) $oaconfig->load('BACKUP_AUTO_CYCLE');
  if ($backup_auto_cycle > $config_backup_auto_cycle) {
      $_SESSION['backup-auto'] = 1;
      if ($logged_admin == true)
      plugtourl('init.php?init=13&auto=1');
  }
}
?>

<!DOCTYPE html>
<html>
  <head>
      <meta charset="utf-8">
      <title><?php echo $website_title; ?></title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="description" content="">
      <meta name="author" content="">

      <!-- With AmazeUI -->
      <link rel="stylesheet" href="includes/assets/css/amazeui.min.css">
      <link rel="stylesheet" href="includes/assets/css/admin.css">
      <link rel="stylesheet" href="includes/assets/css/app.css">
      <link rel="stylesheet" href="includes/assets/css/color.min.css">
      <link rel="stylesheet" href="includes/assets/css/amazeui.magnifier.min.css"/>
      <script src="includes/assets/js/jquery.min.js"></script>
      <script src="includes/assets/js/amazeui.min.js"></script>
      <script src="includes/assets/js/app.js"></script>
      <script src="includes/assets/js/color.min.js"></script>
      <!-- 放大图片 -->
      <script src="includes/assets/js/amazeui.magnifier.min.js"></script>

      <!-- 血与泪得教训啊, 一定要放对位置！ -->
      <script>
          //ajax消息函数
          function msg(data,success,error){
              var id = "#msg";
              if(data=="2"){
                  $(id).attr("class","alert alert-success");
                  $(id).html("<p>"+success+"</p>");
              }else{
                  $(id).attr("class","alert alert-error");
                  $(id).html("<p>"+error+"</p>");
              }
          }

          //延迟刷新或跳转页面模块
          var t ;
          function tourl(t,url){
              t = setTimeout("window.location = '"+url+"'",t);
          }

          //IP地址
          var ip_addr = "<?php echo $ip_arr['addr']; ?>";
          //打印链接处理
          $(document).ready(function() {
              $("a[href='#print_page']").click(function(){
                  window.print();
              });
          });
      </script>

      <style type="text/css">
          body {
              padding-top: 50px;
              overflow:hidden;
          }
      </style>
      <link rel="apple-touch-icon-precomposed" sizes="144x144" href="includes/images/logo-144.png">
      <link rel="apple-touch-icon-precomposed" sizes="114x114" href="includes/images/logo-114.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="includes/images/logo-72.png">
      <link rel="apple-touch-icon-precomposed" href="includes/images/logo-57.png">
      <link rel="shortcut icon" href="includes/images/logo.png">
  </head>

  <body>
    <header class="am-topbar am-topbar-inverse admin-header">
      <div class="am-topbar-brand">
        <strong>默云学习助手</strong>
      </div>

      <button class="am-topbar-btn am-topbar-toggle am-btn am-btn-sm am-btn-success am-show-sm-only" data-am-collapse="{target: '#topbar-collapse'}"><span class="am-sr-only">导航切换</span> <span class="am-icon-bars"></span></button>

      <div class="am-collapse am-topbar-collapse" id="topbar-collapse">

        <ul class="am-nav am-nav-pills am-topbar-nav am-topbar-left admin-header-list">
          <li class="am-active"><a href="init.php"><span class="am-icon-home am-icon-sm"></span> 主页 </a></li>
          <li class="am-dropdown" data-am-dropdown>
            <?php if($logged_admin == false) { ?>
            <a class="am-dropdown-toggle" data-am-dropdown-toggle href="javascript:;">
              <span class="am-icon-user am-icon-sm"></span> 个人中心 <span class="am-icon-caret-down"></span>
            </a>
            <ul class="am-dropdown-content">
              <li><a href="init.php?init=1"><span class="am-icon-envelope"></span> 个人消息</a></li>
              <li><a href="init.php?init=2"><span class="am-icon-cloud"></span> 文件共享</a></li>
              <?php if($logged_teacher == false) { ?>
              <li><a href="init.php?init=3"><span class="am-icon-list"></span> 课堂作业</a></li>
              <li><a href="init.php?init=4"><span class="am-icon-bar-chart"></span> 作业成绩</a></li>
              <?php } ?>
              <li><a href="init.php?init=10"><span class="am-icon-calendar"></span> 班级课表</a></li>
              <li><a href="init.php?init=5"><span class="am-icon-file-text-o"></span> 学习笔记</a></li>
              <li><a href="init.php?init=6"><span class="am-icon-book"></span> 我的好友</a></li>
              <li><a href="http://115.159.91.223/forum/forum.php"><span class="am-icon-comment"></span> 学习论坛</a></li>
              <li><a href="init.php?init=7"><span class="am-icon-cog"></span> 个人设置</a></li>
            </ul>
            <?php } ?>
          </li>
          <li class="am-dropdown" data-am-dropdown>
            <?php if($logged_admin == false) {?>
            <a class="am-dropdown-toggle" data-am-dropdown-toggle href="javascript:;">
              <span class="am-icon-bullhorn am-icon-sm"></span> 共享协作 <span class="am-icon-caret-down"></span>
            </a>
            <ul class="am-dropdown-content">
              <li><a href="init.php?init=8"><span class="am-icon-tags"></span> 笔记共享</a></li>
              <li><a href="init.php?init=9"><span class="am-icon-star"></span> 学习任务</a></li>
              <li><a href="init.php?init=11"><span class="am-icon-list-alt"></span> 公告留言</a></li>
            </ul>
            <?php } ?>
          </li>
          <?php if($logged_admin == true){ ?>
          <li class="am-dropdown" data-am-dropdown>
            <a class="am-dropdown-toggle" data-am-dropdown-toggle href="">
              <span class="am-icon-laptop am-icon-sm"></span>系统设置 <span class="am-icon-caret-down"></span>
            </a>
            <ul class="am-dropdown-content">
              <li><a href="init.php?init=11"><span class="am-icon-list-alt"></span> 公告留言</a></li>
              <li><a href="init.php?init=12"><span class="am-icon-tags"></span> 消息中心</a></li>
              <li><a href="init.php?init=15"><span class="am-icon-user"></span> 用户管理</a></li>
              <li><a href="init.php?init=13"><span class="am-icon-star"></span> 系统设置</a></li>
              <li><a href="init.php?init=14"><span class="am-icon-list-alt"></span> 备份与恢复</a></li>
              <li><a href="init.php?init=16"><span class="am-icon-users"></span> 用户组管理</a></li>
            </ul>
          </li>
          <?php } ?>
          <li><a href="logout.php"><span class="am-icon-sign-out am-icon-sm"></span> 注销</a></li>
          <li class="am-hide-md-down"><a href="javascript:;">
            欢迎您  <b><?php $hello_user = $moyunuser->view_user($moyunuser->get_session_login()); if($hello_user){ echo $hello_user['user_name']; } unset($hello_user); ?></b>  您的IP地址 : <?php echo $ip_arr['addr']; ?></a>
          </li>
        </ul>
      </div>
      </header>

<!-- sidebar start -->
<div class="admin-sidebar am-offcanvas" id="admin-offcanvas">
  <div class="am-offcanvas-bar admin-offcanvas-bar">
    <ul class="am-list admin-sidebar-list">
      <li class="admin-parent">
        <?php if($logged_admin == false) {?>
        <a class="am-cf" data-am-collapse="{target: '#collapse-nav_first'}"><span class="am-icon-user"></span> 个人中心<span class="am-icon-angle-right am-fr am-margin-right"></span></a>
        <ul class="am-list am-collapse admin-sidebar-sub" id="collapse-nav_first">
          <li><a href="init.php?init=1" class="am-cf"><span class="am-icon-envelope"></span> 个人消息<?php if($tip_message_row>0){ ?><span class="am-badge am-fr am-margin-right am-badge-warning"><?php echo $tip_message_row; ?></span><?php } ?></a></li>
          <li><a href="init.php?init=2"><span class="am-icon-cloud"></span> 文件共享</a></li>
          <?php if($logged_teacher == false) { ?>
          <li><a href="init.php?init=3"><span class="am-icon-list"></span> 课堂作业<?php if($tip_task_user_row>0){ ?><span class="am-badge am-fr am-margin-right am-badge-danger"><?php echo $tip_task_user_row; ?></span><?php } ?></a></li>
          <li><a href="init.php?init=4"><span class="am-icon-bar-chart"></span> 作业成绩</a></li>
          <?php } ?>
          <li><a href="init.php?init=10"><span class="am-icon-calendar"></span> 班级课表</a></li>
          <li><a href="init.php?init=5"><span class="am-icon-file-text-o"></span> 学习笔记</a></li>
          <li><a href="init.php?init=6"><span class="am-icon-book"></span> 我的好友</a></li>
          <li><a href="http://115.159.91.223/forum/forum.php"><span class="am-icon-comment"></span> 学习论坛</a></li>
          <li><a href="init.php?init=7"><span class="am-icon-cog"></span> 个人设置</a></li>
        </ul>
        <?php } ?>
      </li>
      <li class="admin-parent">
        <?php if($logged_admin == false) { ?>
        <a class="am-cf" data-am-collapse="{target: '#collapse-nav_second'}"><span class="am-icon-bullhorn"></span> 共享协作 <span class="am-icon-angle-right am-fr am-margin-right"></span></a>
        <ul class="am-list am-collapse admin-sidebar-sub" id="collapse-nav_second">
          <li><a href="init.php?init=8" class="am-cf"><span class="am-icon-tags"></span> 笔记共享</a></li>
          <li><a href="init.php?init=9"><span class="am-icon-star"></span> 学习任务</a></li>
          <li><a href="init.php?init=11"><span class="am-icon-list-alt"></span> 公告留言</a></li>
        </ul>
        <?php } ?>
      </li>
      <?php if($logged_admin == true){ ?>
      <li class="admin-parent">
        <a class="am-cf" data-am-collapse="{target: '#collapse-nav_third'}"><span class="am-icon-laptop"></span> 系统设置 <span class="am-icon-angle-right am-fr am-margin-right"></span></a>
        <ul class="am-list am-collapse admin-sidebar-sub am-in" id="collapse-nav_third">
          <li><a href="init.php?init=11"><span class="am-icon-list-alt"></span> 公告留言</a></li>
          <li><a href="init.php?init=12"><span class="am-icon-tags"></span> 消息中心</a></li>
          <li><a href="init.php?init=15"><span class="am-icon-user"></span> 用户管理</a></li>
          <?php if($logged_admin == true) { ?>
          <li><a href="init.php?init=13"><span class="am-icon-star"></span> 系统设置</a></li>
          <li><a href="init.php?init=14"><span class="am-icon-list-alt"></span> 备份与恢复</a></li>
          <li><a href="init.php?init=16"><span class="am-icon-users"></span> 用户组管理</a></li>
          <?php } ?>
        </ul>
      </li>
      <?php } ?>
      <!-- <li><a href="logout.php"><span class="am-icon-sign-out"></span> 注销</a></li> -->
    </ul>

    <div class="am-panel am-panel-default admin-sidebar-panel">
      <div class="am-panel-bd">
        <p><span class="am-icon-tag"></span> wiki</p>
        <p>Welcome to the MOYUN wiki!</p>
      </div>
    </div>
  </div>
</div>

<?php
/**
 * 引入内部内容
 * @since 4
 */
require('init_' . $init_page_arr[$init_page] . '.php');
?>


<footer>
    <p>
        <?php
        echo $website_footer;
        ?>
    </p>
</footer>


  </body>
</html>
