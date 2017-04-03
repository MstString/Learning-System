<?php
/**
 * 修改个人信息页面
 * @author string.zhengyang@gmail.com
 * @version 2
 * @package MOYUN
 */
if (isset($init_page) == false) {
    die();
}

/**
 * 获取用户信息
 */
$self_user = $moyunuser->view_user($moyunuser->get_session_login());

/**
 * 编辑用户信息
 */
//编辑是否成功标记
$self_edit_bool = false;
if (isset($_POST['edit_email']) == true && isset($_POST['edit_name']) == true) {
    $password = null;
    //如果提交了密码
    if (isset($_POST['edit_password']) == true && isset($_POST['edit_new_password']) == true && isset($_POST['edit_new_password2']) == true) {
        if ($_POST['edit_new_password'] === $_POST['edit_new_password2']) {
            $password = $_POST['edit_new_password'];
        }
    }
    $self_edit_bool = $moyunuser->edit_user($self_user['id'], $self_user['user_username'], $password, $_POST['edit_email'], $_POST['edit_name'], $self_user['user_group']);
}

//如果编辑成功则重新获取用户信息
if ($self_edit_bool == true) {
    $self_user = $moyunuser->view_user($moyunuser->get_session_login());
}

//如果用户信息获取失败
if (!$self_user) {
    plugerror('selferror');
}
?>

<!-- 修改个人信息 -->
<div class="admin-content">
    <div class="admin-content-body">

      <div class="am-cf am-padding">
        <div class="am-fl am-cf">
          <strong class="am-text-primary am-text-lg">个人信息编辑</strong> / <small>Change Something...</small>
        </div>
      </div>

      <hr>

      <form class="am-form am-form-horizontal" action="init.php?init=7" method="post">
        <div class="am-form-group am-g-fixed">
            <label for="edit_email" class="am-u-sm-3 am-form-label">邮箱 / Email</label>
            <div class="am-u-sm-9 am-form-group am-button-group">
              <input type="text" id="edit_email" name="edit_email" placeholder="@邮箱.com" value="<?php echo $self_user['user_email']; ?>">
              <small>Your Email Name...</small>
            </div>
        </div>
        <div class="am-form-group am-g-fixed">
            <label for="edit_name" class="am-u-sm-3 am-form-label">昵称 / Nick Name</label>
            <div class="am-u-sm-9 am-form-group am-button-group">
              <input type="text" id="edit_name" name="edit_name" placeholder="昵称" value="<?php echo $self_user['user_name']; ?>">
              <small>Your Nick Name...</small>
            </div>
        </div>
        <div class="am-form-group am-g-fixed">
            <label for="edit_password" class="am-u-sm-3 am-form-label">修改登录密码</label>
            <div class="am-u-sm-9 am-form-group am-button-group">
              <input type="password" id="edit_password" name="edit_password" placeholder="当前密码（不修改留空）" >
              <small>Your original Password...</small>
            </div>
        </div>
        <div class="am-form-group am-g-fixed">
            <label for="edit_new_password" class="am-u-sm-3 am-form-label">新密码</label>
            <div class="am-u-sm-9 am-form-group am-button-group">
              <input type="password" id="edit_new_password" name="edit_new_password" placeholder="新密码">
              <small>Your New Password...</small>
            </div>
        </div>
        <div class="am-form-group am-g-fixed">
            <label for="edit_new_password2" class="am-u-sm-3 am-form-label">新密码（重复）</label>
            <div class="am-u-sm-9 am-form-group am-button-group">
              <input type="password" id="edit_new_password2" name="edit_new_password2" placeholder="重复新密码">
              <small>Your New Password...</small>
              <hr>
              <a id="msg"></a>
              <button type="submit" class="am-btn am-radius am-btn-sm color-btn" data-am-popover="{theme: 'warning', content: '你确定修改吗？', trigger: 'hover focus'}"> 修改</button>&nbsp;
              <button href="#return" type="button" class="am-btn am-radius am-btn-sm color-btn" data-am-popover="{theme: 'warning', content: '返回', trigger: 'hover focus'}"> 取消</button>
            </div>
        </div>
      </form>
    </div>
  </div>

<!-- script -->
<script>
    $(document).ready(function() {
        //默认值
        var default_email = "<?php echo $self_user['user_email']; ?>";
        var default_name = "<?php echo $self_user['user_name']; ?>";
        var is_edit = <?php if(isset($_POST['edit_email']) == true){ echo 'true'; }else{ echo 'false'; } ?>;
        var is_edit_r = "<?php if($self_edit_bool == true){ echo '2'; }else{ echo '1'; } ?>";
        if(is_edit){
            msg(is_edit_r,"修改用户信息成功！","无法修改用户信息，请稍候重试。");
        }
        //复原按钮事件
        $("button[href='#return']").click(function() {
            $("#edit_email").val(default_email);
            $("#edit_name").val(default_name);
            $("#edit_password").val("");
            $("#edit_new_password").val("");
            $("#edit_new_password2").val("");
        });
    });
</script>
