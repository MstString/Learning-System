<?php
/**
 * 系统设置中心
 * @author string.zhengyang@gmail.com
 * @version 6
 * @package MOYUN
 */
if (isset($init_page) == false) {
    die();
}

/**
 * 操作消息内容
 */
$message = '';
$message_bool = false;

/**
 * 编辑失败消息
 * @since 1
 * @global string $message
 * @global string $message_bool
 * @param string $msg 失败的消息
 */
function message_config_false($msg) {
    global $message, $message_bool;
    if ($message_bool == true) {
        $message = '修改系统设置成功！';
    } else {
        $message = $msg;
    }
}

/**
 * 编辑系统设置
 * @since 6
 */
if (isset($_GET['edit']) == true) {
    //网站标题
    if (isset($_POST['config_web_title']) == true) {
        if ($_POST['config_web_title'] && strlen($_POST['config_web_title']) > 1 && strlen($_POST['config_web_title']) < 300) {
            $message_bool = $oaconfig->save('WEB_TITLE', $_POST['config_web_title']);
        }
    }
    message_config_false('无法修改网站标题。');
    //网站开关
    if ($message_bool == true && isset($_POST['config_web_on']) == true) {
        $config_web_on = $_POST['config_web_on'] ? '1' : '0';
        $message_bool = $oaconfig->save('WEB_ON', (int) $config_web_on);
        message_config_false('无法修改网站开关状态。');
    }
    //用户登录超时时效
    if ($message_bool == true && isset($_POST['config_user_timeout']) == true) {
        $message_bool = false;
        if ($_POST['config_user_timeout'] && $_POST['config_user_timeout'] >= 120 && $_POST['config_user_timeout'] <= 999999) {
            $message_bool = $oaconfig->save('USER_TIMEOUT', (int) $_POST['config_user_timeout']);
        }
        message_config_false('无法修改用户登录超时时间。');
    }
    //网站地址
    if (isset($_POST['config_web_url'])) {
        $message_bool = $oaconfig->save('WEB_URL', $_POST['config_web_url']);
        message_config_false('无法修改网站地址。');
    }
    //上传功能开关
    if (isset($_POST['config_uploadfile_on'])) {
        $config_uploadfile_on = $_POST['config_uploadfile_on'] ? '1' : '0';
        $message_bool = $oaconfig->save('UPLOADFILE_ON', (int) $config_uploadfile_on);
        message_config_false('无法修改上传功能开关。');
    }
    //上传禁用类型
    if (isset($_POST['config_uploadfile_inhibit_type'])) {
        $config_inhibit_type = '';
        if ($_POST['config_uploadfile_inhibit_type']) {
            $config_inhibit_type = $_POST['config_uploadfile_inhibit_type'];
        }
        $message_bool = $oaconfig->save('UPLOADFILE_INHIBIT_TYPE', $config_inhibit_type);
        message_config_false('无法修改上传文件禁止类型。');
    }
    //上传大小最小
    if (isset($_POST['config_uploadfile_size_min'])) {
        $message_bool = $oaconfig->save('UPLOADFILE_SIZE_MIN', (int) $_POST['config_uploadfile_size_min']);
        message_config_false('无法修改上传文件最小限制。');
    }
    //上传大小最大
    if (isset($_POST['config_uploadfile_size_max'])) {
        $message_bool = $oaconfig->save('UPLOADFILE_SIZE_MAX', (int) $_POST['config_uploadfile_size_max']);
        message_config_false('无法修改上传文件最大限制。');
    }
    //业绩加权(最终业绩将乘以该值)
    if (isset($_POST['config_performance_scale'])) {
        $message_bool = $oaconfig->save('PERFORMANCE_SCALE', (int) $_POST['config_performance_scale']);
        message_config_false('无法修改业绩加权。');
    }
    //自动备份开关
    if ($message_bool == true && isset($_POST['config_backup_auto_on']) == true) {
        $config_backup_auto_on = $_POST['config_backup_auto_on'] ? '1' : '0';
        $message_bool = $oaconfig->save('BACKUP_AUTO_ON', (int) $config_backup_auto_on);
        message_config_false('无法修改自动备份开关。');
    }
    //自动备份周期
    if ($message_bool == true && isset($_POST['config_backup_auto_cycle']) == true) {
        $message_bool = false;
        $config_backup_auto_cycle = (int) $_POST['config_backup_auto_cycle'];
        if ($config_backup_auto_cycle > 0) {
            $message_bool = $oaconfig->save('BACKUP_AUTO_CYCLE', $config_backup_auto_cycle);
        }
        message_config_false('无法修改自动备份周期。');
    }
    //备份文件存储目录
    if (isset($_POST['config_backup_dir'])) {
        $config_backup_dir = $oaconfig->save('BACKUP_DIR', $_POST['config_backup_dir']);
        message_config_false('无法修改备份目录。');
    }
}

/**
 * 还原系统设置
 * @since 1
 */
if(isset($_GET['return']) == true){
    $message_bool = $oaconfig->return_default_all();
    if($message_bool){
        $message = '还原设置成功，现在系统所有设置均恢复到最初状态。';
    }else{
        $message = '无法还原系统设置，可能是某些参数正在被修改。';
    }
}
?>
<!-- 系统设置 -->
<div class="admin-content">
    <div class="admin-content-body">

      <div class="am-cf am-padding">
        <div class="am-fl am-cf">
          <strong class="am-text-primary am-text-lg">系统设置</strong> / <small>System Setting</small>
        </div>
      </div>
      <hr>
      <div class="am-g">
        <div class="am-u-sm-12">

          <form class="am-form form-actions am-form-horizontal" action="init.php?init=13&edit=1" method="post">
            <div class="am-form-group am-g-fixed">
                <label for="config_web_title" class="am-u-sm-3 am-form-label">网站名称</label>
                <div class="am-u-sm-9 am-form-group am-button-group">
                  <input type="text" id="config_web_title" name="config_web_title" value="<?php echo $oaconfig->load('WEB_TITLE'); ?>">
                  <small>Your Web Name...</small>
                </div>
            </div>
            <div class="am-form-group am-g-fixed">
                <label for="config_web_on" class="am-u-sm-3 am-form-label">网站状态</label>
                <div class="am-u-sm-9 am-form-group am-button-group doc-js-btn-1">
                  <button id="doc-single-toggle" type="button" value="1" class="am-btn color-btn" data-am-popover="{theme:'warning', content: '打开网站', trigger: 'hover focus'}"  data-am-button>
                      开启
                  </button>
                  <button id="doc-single-toggle" type="button" value="0" class="am-btn color-btn" data-am-popover="{theme:'warning', content: '关闭网站，非管理员无法登陆。', trigger: 'hover focus'}">
                      关闭
                  </button>
                </div>
                <div class="hidden" style="overflow: hidden; text-indent: -9999px;">
                    <input type="text" name="config_web_on" value="<?php echo $oaconfig->load('WEB_ON'); ?>">
                </div>
            </div>
            <div class="am-form-group am-g-fixed">
                <label for="config_web_url" class="am-u-sm-3 am-form-label">网站域名</label>
                <div class="am-u-sm-9 am-form-group am-button-group">
                  <input type="text" id="config_web_url" name="config_web_url" value="<?php echo $oaconfig->load('WEB_URL'); ?>">
                  <small>Your Web Address...</small>
                </div>
            </div>
            <div class="am-form-group am-g-fixed">
                <label for="config_user_timeout" class="am-u-sm-3 am-form-label">网站登陆超时设置</label>
                <div class="am-u-sm-9 am-form-group am-button-group">
                  <input type="text" id="config_user_timeout" name="config_user_timeout" value="<?php echo $oaconfig->load('USER_TIMEOUT'); ?>">
                </div>
            </div>
            <div class="am-form-group am-g-fixed">
                <label for="config_uploadfile_on" class="am-u-sm-3 am-form-label">文件上传设置</label>
                <div class="am-u-sm-9 am-form-group am-button-group doc-js-btn-1">
                  <button id="doc-single-toggle" type="button" value="1" class="am-btn color-btn" data-am-popover="{theme:'warning', content: '打开上传功能', trigger: 'hover focus'}"  data-am-button>
                      开启
                  </button>
                  <button id="doc-single-toggle" type="button" value="0" class="am-btn color-btn" data-am-popover="{theme:'warning', content: '关闭上传功能', trigger: 'hover focus'}">
                      关闭
                  </button>
                </div>
                <div class="hidden" style="overflow: hidden; text-indent: -9999px;">
                    <input type="text" name="config_uploadfile_on" value="<?php echo $oaconfig->load('UPLOADFILE_ON'); ?>">
                </div>
            </div>
            <div class="am-form-group am-g-fixed">
                <label for="config_uploadfile_inhibit_type" class="am-u-sm-3 am-form-label">禁止上传文件类型</label>
                <div class="am-u-sm-9 am-form-group am-button-group">
                  <input type="text" id="config_uploadfile_inhibit_type" name="config_uploadfile_inhibit_type" value="<?php echo $oaconfig->load('UPLOADFILE_INHIBIT_TYPE'); ?>">
                </div>
            </div>
            <div class="am-form-group am-g-fixed">
                <label for="config_uploadfile_inhibit_type" class="am-u-sm-3 am-form-label">上传文件大小设置</label>
                <div class="am-u-sm-3 am-form-group am-button-group">
                  <input type="text" id="config_uploadfile_size_min" name="config_uploadfile_size_min" placeholder="MIN(KB)" value="<?php echo $oaconfig->load('UPLOADFILE_SIZE_MIN'); ?>KB">
                </div>
                <div class="am-u-sm-6 am-form-group am-button-group">
                  <input type="text" id="config_uploadfile_size_max" name="config_uploadfile_size_max" placeholder="MAX(KB)" value="<?php echo $oaconfig->load('UPLOADFILE_SIZE_MAX'); ?>KB">
                </div>
            </div>
            <div class="am-form-group am-g-fixed">
                <label for="config_backup_auto_on" class="am-u-sm-3 am-form-label">自动备份</label>
                <div class="am-u-sm-9 am-form-group am-button-group doc-js-btn-1">
                  <button id="doc-single-toggle" type="button" value="1" class="am-btn color-btn" data-am-popover="{theme:'warning', content: '打开上备份功能', trigger: 'hover focus'}"  data-am-button>
                      开启
                  </button>
                  <button id="doc-single-toggle" type="button" value="0" class="am-btn color-btn" data-am-popover="{theme:'warning', content: '关闭备份功能', trigger: 'hover focus'}">
                      关闭
                  </button>
                </div>
                <div class="hidden" style="overflow: hidden; text-indent: -9999px;">
                    <input type="config_backup_auto_on" name="config_backup_auto_on" value="<?php echo $oaconfig->load('BACKUP_AUTO_ON'); ?>">
                </div>
            </div>
            <div class="am-form-group am-g-fixed">
                <label for="config_backup_auto_cycle" class="am-u-sm-3 am-form-label">自动备份周期</label>
                <div class="am-u-sm-9 am-form-group am-button-group">
                  <input type="text" id="config_backup_auto_cycle" name="config_backup_auto_cycle" placeholder="天数" value="<?php echo $oaconfig->load('BACKUP_AUTO_CYCLE'); ?>">
                </div>
            </div>
            <div class="am-form-group am-g-fixed">
                <label for="config_backup_dir" class="am-u-sm-3 am-form-label">自动备份目录</label>
                <div class="am-u-sm-9 am-form-group am-button-group">
                  <input type="text" id="config_backup_dir" name="config_backup_dir" value="<?php echo $oaconfig->load('BACKUP_DIR'); ?>">
                  <hr>
                    <a id="msg"></a>
                    <button type="submit" class="am-btn am-radius am-btn-sm am-fl color-btn" type="submit" data-am-popover="{theme: 'warning', content: '修改系统设置', trigger: 'hover focus'}"><i class="am-icon-pencil"></i> 修改</button>&nbsp;
                    <a href="init.php?init=13&return=1" role="button" class="am-btn color-btn" data-am-popover="{theme:'warning', content: '还原系统设置', trigger: 'hover focus'}"><i class="am-icon-reply"></i> 还原</a>
                </div>
            </div>
          </form>

        </div>
      </div>

    </div>
  </div>

<!-- Javascript -->
<script>
    $(document).ready(function(){
        var message = "<?php echo $message; ?>";
        var message_bool = "<?php echo $message_bool ? '2' : '1'; ?>";
        if(message != ""){
            msg(message_bool,message,message);
        }
        //单选按钮和input值关联
        $("div[data-toggle='buttons-radio'] > button").click(function(){
            $(this).parent().next().children().attr("value",$(this).attr("value"));
        });
        //遍历所有单选并设定值
        $("div[data-toggle='buttons-radio']").each(function(i,dom){
            var value = $(dom).next().children().attr("value");
            $(dom).children("button").each(function(j,dom_c){
                if($(dom_c).attr("value") == value){
                    $(dom_c).attr("class",$(dom_c).attr("class")+" active");
                    return;
                }
            });
        });
    });
</script>
