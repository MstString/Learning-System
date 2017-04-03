<?php
/**
 * 错误响应页面
 * @author string.zhengyang@gmail.com
 * @version 2
 * @package MOYUN
 */

/*
*显示处错误信息
*/
$error_arr = array(
    'login-vcode' => '验证码输入有误， 请看仔细后重新输入哦！',
    'login' => '登陆失败，可能是您的用户名或密码错误！',
    'logged' => '您还没有登陆，或者操作超时，请尝试重新登陆。',
    'noadmin' => '您不是管理员，无法访问该页面。',
    'selferror'=>'无法获取用户数据，请尝试重新登录。',
    'downloadfile-pw'=>'该文件被加密了，您必须输入密码才能访问。',
    'webclose'=>'网站已经关闭了。');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>小♥偶</title>
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="description" content="这是一个404页面">
        <meta name="keywords" content="404">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="renderer" content="webkit">
        <meta http-equiv="Cache-Control" content="no-siteapp" />
        <link rel="icon" type="image/png" href="includes/assets/i/favicon.png">
        <link rel="apple-touch-icon-precomposed" href="includes/assets/i/app-icon72x72@2x.png">
        <meta name="apple-mobile-web-app-title" content="Amaze UI" />
        <link rel="stylesheet" href="includes/assets/css/amazeui.min.css"/>
        <link rel="stylesheet" href="includes/assets/css/admin.css">
        <!-- Le styles -->
        <style type="text/css">
            body {
                padding-top: 60px;
                padding-bottom: 40px;
            }
        </style>

        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
          <script src="includes/js/html5shiv.js"></script>
        <![endif]-->

        <!-- Fav and touch icons -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="includes/images/logo-144.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="includes/images/logo-114.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="includes/images/logo-72.png">
        <link rel="apple-touch-icon-precomposed" href="includes/images/logo-57.png">
        <link rel="shortcut icon" href="includes/images/logo.png">
    </head>

    <body>
      <header class="am-topbar am-topbar-inverse admin-header">
          <div class="am-topbar-brand">
            <strong>MOYUN</strong> <small>错误界面</small>
          </div>
          <button class="am-topbar-btn am-topbar-toggle am-btn am-btn-sm am-btn-success am-show-sm-only" data-am-collapse="{target: '#topbar-collapse'}"><span class="am-sr-only">导航切换</span> <span class="am-icon-bars"></span></button>
          <div class="am-collapse am-topbar-collapse" id="topbar-collapse">
            <ul class="am-nav am-nav-pills am-topbar-nav am-topbar-right admin-header-list">
              <li><a href="javascript:;"></a></li>
              <li class="am-dropdown" data-am-dropdown="">
                <a class="am-dropdown-toggle" data-am-dropdown-toggle="" href="javascript:;">
                  <a class="am-icon-sign-out" href="index.php" target="_self"> 返回首页</a>
                </a>
              </li>
            </ul>
          </div>
        </header>

        <!-- This is AmazeUI -->
        <div class="am-g">
          <div class="am-u-sm-12">
            <h2 class="am-text-center am-text-xl am-margin-top-lg">404. Not Found</h2>
            <p class="am-text-center">
              <?php
              if (isset($_GET['e']) == true && is_string($_GET['e']) == true) {
                  if (isset($error_arr[$_GET['e']]) == true) {
                      echo $error_arr[$_GET['e']];
                  }
              }
              ?>
            </p>
          <pre class="page-404">
            .----.
         _.'__    `.
     .--(♥)(♥)---/#\
   .' @          /###\
   :         ,   #####
    `-..__.-' _.-\###/
          `;_:    `"'
        .'"""""`.
       /, MOYUN ,\\Li
      //  404!    \\
      `-._______.-'
      ___`. | .'___
     (______|______)
     ```````````````````````````````
     ``````````````````````````````````````````
          </pre>
          <h4>
          </h4>
          </div>
              </div>
            </div>
        </div> <!-- /container -->
        <!-- Le javascript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <!-- 3.00 -->
        <script src="includes/assets/js/jquery.min.js"></script>
        <script src="includes/assets/js/amazeui.min.js"></script>
        <script src="includes/assets/js/app.js"></script>
    </body>
</html>
