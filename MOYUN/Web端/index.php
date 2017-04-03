<?php
/**
* MOYUN助手登录首页
* @author string.zhengyang@gmail.com
* @version 1
* @package MOYUN
*/
require('glob.php');
?>
<!DOCTYPE html>
<html>
<!-- this no-js is with Modernizr -->
<head>
<meta charset="utf-8">
<!-- with the old brower -->
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php echo $website_title; ?> - 登录首页</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="author" content="">

<!-- Set render engine for 360 browser -->
<meta name="renderer" content="webkit">

<!-- No Baidu Siteapp-->
<meta http-equiv="Cache-Control" content="no-siteapp"/>

<!-- Add to homescreen for Chrome on Android -->
<meta name="mobile-web-app-capable" content="yes">
<link rel="icon" sizes="192x192" href="includes/assets/i/app-icon72x72@2x.png">

<!-- Fav and touch icons -->  <!-- Add to homescreen for Safari on iOS -->
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="includes/images/logo-144.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="includes/images/logo-114.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="includes/images/logo-72.png">
<link rel="apple-touch-icon-precomposed" href="includes/images/logo-57.png">
<link rel="shortcut icon" href="includes/images/logo.png">

<!-- Tile icon for Win8 (144x144 + tile color) -->
<meta name="msapplication-TileImage" content="includes/assets/i/app-icon72x72@2x.png">
<meta name="msapplication-TileColor" content="#0e90d2">


<!-- With AmazeUI -->
<link rel="stylesheet" href="includes/assets/css/amazeui.min.css">
<link rel="stylesheet" href="includes/assets/css/app.css">
<link rel="stylesheet" href="includes/assets/css/color.min.css">

<style type="text/css">
  .header {
    text-align: center;
  }
  .header h1 {
    font-size: 200%;
    color: #333;
    margin-top: 30px;
  }
  .header p {
    font-size: 14px;
  }
</style>
<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="includes/js/html5shiv.js"></script>
<![endif]-->
</head>

<body>
<div class="header">
<div class="am-g">
  <h1><?php echo $website_title; ?></h1>
  <p>Just For Dream<br/>交流 创新 为梦想而战！</p>
</div>
<hr/>
</div>
<div class="am-g">
<div class="am-u-lg-6 am-u-md-8 am-u-sm-centered">
<h3>登录</h3>
<hr>
<div class="am-btn-group">
  <button type="button" class="am-btn am-radius am-btn-sm color-btn" data-am-modal="{target: '#doc-modal-1'}">
    <i class="am-icon-qq am-icon-sm"></i> QQ
  </button>
  <div class="am-modal am-modal-no-btn color-modal-style color-modal-style-blue" tabindex="-1" id="doc-modal-1">
    <div class="am-modal-dialog">
        <div class="color-modal-style-head am-text-middle">
            <span class="color-modal-style-head-title ">
                Sorry /(ㄒoㄒ)/~~
            </span>
        </div>
        <div class="am-modal-hd">
            <a href="javascript: void(0)" class="am-close am-close-spin  color-modal-style-close" data-am-modal-close>×</a>
        </div>
        <div class="am-modal-bd">
            QQ接口我还没有实现了。。。(lll￢ω￢)
        </div>
        <div class="color-modal-style-btn">
            <button type="button" class="am-btn am-radius" data-am-modal-close>摸摸头</button>
        </div>
    </div>
  </div>
<button type="button" class="am-btn am-btn-sm am-radius color-btn" data-am-modal="{target: '#doc-modal-2'}">
  <i class="am-icon-weixin am-icon-sm"></i> 微信
</button>
<div class="am-modal am-modal-no-btn color-modal-style color-modal-style-green" tabindex="-1" id="doc-modal-2">
  <div class="am-modal-dialog">
      <div class="color-modal-style-head am-text-middle">
          <span class="color-modal-style-head-title ">
              Sorry /(ㄒoㄒ)/~~
          </span>
      </div>
      <div class="am-modal-hd">
          <a href="javascript: void(0)" class="am-close am-close-spin  color-modal-style-close" data-am-modal-close>×</a>
      </div>
      <div class="am-modal-bd">
          微信接口我也还没有实现了。。。(lll￢ω￢)
      </div>
      <div class="color-modal-style-btn">
          <button type="button" class="am-btn am-radius" data-am-modal-close>我差不多是一个废人了</button>
      </div>
  </div>
</div>

<a href="http://115.159.91.223/forum" type="button" class="am-btn am-btn-sm color-btn" data-am-popover="{theme: 'warning', content: '交流论坛', trigger: 'hover focus'}">
  <i class="am-icon-github am-icon-sm"></i> 论坛
</a>

</div>
<br/>
<br/>
<!-- 发送post请求, 通过login.php -->
<form class="am-form" action="login.php" method="post">
    <!-- <input name="user" type="text" class="input-block-level" placeholder="用户名" value=""> -->
    <input name="user" type="text" autocomplete="off" class="input-block-level am-form-field color-word-count color-test-word" placeholder="用户名" value="">
    <div class="color-padding-top-xs">
        还可以输入
        <span class="color-word-count-counter">11</span>
        字。
    </div>
    <hr/>
    <input name="pass" type="password" autocomplete="off" class="input-block-level" placeholder="密码" value="">
    <hr/>
    <input name="vcode" type="text" autocomplete="off" class="input-block-level" placeholder="验证码" value="">
    <hr/>
    <a href="#"><img onclick="javascript:$('img').attr('src', 'vcode.php?r=' + Math.random());" src="vcode.php" style="width:150px;height:35px;"></a>
    <button class="am-btn am-btn-success am-radius am-btn-sm am-fr color-btn" type="submit" style="width:150px;height:35px;" data-am-popover="{theme:'warning', content: '发现一个更简单的世界', trigger: 'hover focus'}">登&nbsp;&nbsp;&nbsp;&nbsp;陆</button>
</form>
<hr/>
<p>© 2016 by The MOYUN Team.</p>
</div>
</div>
<!--[if (gte IE 9)|!(IE)]><!-->
<script src="includes/assets/js/jquery.min.js"></script>
<!--<![endif]-->
<script src="includes/assets/js/amazeui.min.js"></script>
<script src="includes/assets/js/color.min.js"></script>
<!-- script代码位置很重要 -->
<script>
$('.color-test-word').WordCount({
  max: 11, // 最大长度,如果不传会去取文本框的maxlength
  isOverflowCut: false, // 是否自动截取文本
  overClass: "color-word-count-over", // 超出文本时的样式,会同时在num上和textbox上添加
  num: $(".color-word-count-counter"), // 显示计数的结点
  withButton: ".click", // 关联按钮
  // minHeight: 100, // 文本框的最小高度，因为这里做自适应高度的控制。如没有此参数时，不自适应高度。
  overflowCallback: function() {
      //超出时的回调，this指向当前对象,n为长度,textbox是文本框结点对象,max为最大长度
  },
  changeCallback: function(num) {
      //长度改变时的回调,n为长度,textbox是文本框结点对象,max为最大长度
  },
  passClallback: function() {
      // 长度通过时的回调,n为长度,textbox是文本框结点对象,max为最大长度
  },
  isByte: true //是否按字节数来计算，true时：一个汉字作2个单位长度，false时汉字英文不作区分
});
</script>
</body>
</html>
