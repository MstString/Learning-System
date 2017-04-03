<?php
/**
 * 消息中心页面
 * @author string.zhengyang@gmail.com
 * @version 6
 * @package MOYUN
 */
if (isset($init_page) == false) {
    die();
}

/**
 * 操作消息内容
 * @since 1
 */
$message = '';
$message_bool = false;

/**
 * 添加新的消息
 * @since 4
 */
if (isset($_POST['new_message']) == true) {
    $title = '';
    //引入截取字符串模块
    require_once(DIR_LIB . DS . 'plug-substrutf8.php');
    if (isset($_POST['new_title']) == true && $_POST['new_title']) {
        $title = plugsubstrutf8($_POST['new_title'], 15);
    } else {
        $title = plugsubstrutf8($_POST['new_message'], 15);
    }
    if ($oapost->add($title, $_POST['new_message'], 'message', 0, $moyunuser->get_session_login(), null, null, null, 'public', null)) {
        $message = '添加通知成功！';
        $message_bool = true;
    } else {
        $message = '无法添加新的通知。';
        $message_bool = false;
    }
}

/**
 * 编辑消息
 * @since 4
 */
if (isset($_POST['edit_id']) == true && isset($_POST['edit_message']) == true) {
    $title = '';
    //引入截取字符串模块
    require_once(DIR_LIB . DS . 'plug-substrutf8.php');
    if (isset($_POST['edit_title']) == true && $_POST['edit_title']) {
        $title = plugsubstrutf8($_POST['edit_title'], 15);
    } else {
        $title = plugsubstrutf8($_POST['edit_message'], 15);
    }
    if ($oapost->edit($_POST['edit_id'], $title, $_POST['edit_message'], 'message', 0, $moyunuser->get_session_login(), null, null, null, 'public', null)) {
        $message = '编辑通知成功！';
        $message_bool = true;
    } else {
        $message = '无法修改通知，请稍候重试。';
        $message_bool = false;
    }
}

/**
 * 删除消息
 * @since 1
 */
if (isset($_GET['del']) == true) {
    if($oapost->del($_GET['del'])){
        $message = '删除通知成功！';
        $message_bool = true;
    }else{
        $message = '无法删除该通知。';
        $message_bool = false;
    }
}

/**
 * 初始化变量
 * @since 1
 */
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$max = 10;
$sort = 0;
$desc = true;
$post_user = null;
if (isset($_GET['user']) == true) {
    $post_user = $_GET['user'];
}

/**
 * 获取消息列表记录数
 * @since 5
 */
$message_list_row = $oapost->view_list_row($post_user, null, null, 'public', 'message', null, null);

/**
 * 计算页码
 * @since 1
 */
$page_max = ceil($message_list_row / $max);
if ($page < 1) {
    $page = 1;
} else {
    if ($page > $page_max) {
        $page = $page_max;
    }
}
$page_prev = $page - 1;
$page_next = $page + 1;

/**
 * 获取消息列表
 * @since 5
 */
$message_list = $oapost->view_list($post_user, null, null, 'public', 'message', $page, $max, $sort, $desc, null, null);
?>
<!-- 管理表格 -->
<div class="admin-content">
    <div class="admin-content-body">

      <div class="am-cf am-padding">
        <div class="am-fl am-cf">
          <strong class="am-text-primary am-text-lg">系统消息中心</strong> / <small>System Message Center</small>
        </div>
      </div>
      <hr>

      <div class="am-g">
        <div class="am-u-sm-12">
          <form class="am-form">
            <div>
            <a id="msg"></a>
            </div>
            <table class="am-table am-table-bordered am-table-hover color-table-bordered am-table-centered">
            <thead>
            <tr>
              <th class="am-hide-md-down"><span class="am-icon-list"></span> ID</th>
              <th class="table-author"><span class="am-icon-user"></span> 发布用户</th>
              <th class="am-hide-md-down"><span class="am-icon-calendar"></span> 发表时间</th>
              <th><i class="am-icon-tag"></i> 标题</th>
              <th class="table-set"><span class="am-icon-gear"></span> 操作</th>
            </tr>
            </thead>
            <tbody id="message_list">
                <?php if($message_list){ foreach($message_list as $v){ ?>
                <tr>
                    <td><?php echo $v['id']; ?></td>
                    <td><?php $message_user = $moyunuser->view_user($v['post_user']); if($message_user){ echo '<a href="init.php?init=12&user='.$message_user['id'].'" target="_self">'.$message_user['user_name'].'</a>'; unset($message_user); } ?></td>
                    <td><?php echo $v['post_date']; ?></td>
                    <td><a href="init.php?init=12&view=<?php echo $v['id']; ?>#view" target="_self"><?php echo $v['post_title']; ?></a></td>
                    <td>
                      <div class="btn-group">
                        <a href="init.php?init=12&edit=<?php echo $v['id']; ?>#edit"><div class="am-btn am-btn-success am-btn-xs am-radius"><i class="am-icon-pencil"></i> 编辑</div></a>&nbsp;
                        <a href="init.php?init=12&del=<?php echo $v['id']; ?>"><div class="am-btn am-btn-danger am-btn-xs am-radius"><i class="am-icon-trash"></i> 删除</div></a>
                      </div>
                    </td>
                </tr>
                <?php } } ?>
            </tbody>
          </table>
        </form>
      </div>
    </div>

        <div class="am-cf">
          <ul class="am-pagination am-default admin-content-pagination" style="padding-left: 25px;">
            <li class="am-<?php if($page<=1){ echo ' disabled'; } ?>" style="float:left;"><a href="<?php echo $page_url.'&page='.$page_prev; ?>">&laquo;上一页</a></li>
            <li class="am-<?php if($page>=$page_max){ echo ' disabled'; } ?>" style="float:right; margin-right:25px;"><a href="<?php echo $page_url.'&page='.$page_next;?>">下一页&raquo;</a></li>
          </ul>
        </div>

        <hr>

        <?php
        if (isset($_GET['edit']) == false && isset($_GET['view']) == false) {
            ?>
            <div class="am-cf am-padding">
              <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">发布系统通知</strong> / <small>看仔细了哦!</div>
            </div>
            <hr>
            <form class="am-form am-form-horizontal form-actions" action="init.php?init=12" method="post">
              <div class="am-form-group am-g-fixed">
                  <label for="new_title" class="am-u-sm-3 am-form-label">标题 / System Session Title</label>
                  <div class="am-u-sm-9 am-form-group am-button-group">
                    <input type="text" id="new_title" name="new_title" placeholder="标题(可留空)">
                  </div>
              </div>
              <div class="am-form-group am-g-fixed">
                  <label for="new_message" class="am-u-sm-3 am-form-label">系统通知内容 / System Content</label>
                  <div class="am-u-sm-9 am-form-group am-button-group">
                    <textarea rows="5" id="new_message" name="new_message" placeholder="系统通知内容……"></textarea>
                  <hr>
                  <button type="submit" class="am-btn am-radius am-btn-sm am-fl color-btn" type="submit" data-am-popover="{theme: 'warning', content: 'Believe yourself.', trigger: 'hover focus'}"><i class="am-icon-pencil"></i> 发布</button>
                  </div>
              </div>
            </form>

            <?php
    }
    if (isset($_GET['edit']) == true && isset($_GET['view']) == false) {
        $edit_message = $oapost->view($_GET['edit']);
        if ($edit_message) {
            ?>
            <!-- 编辑通知 -->
            <div class="am-cf am-padding">
              <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">编辑系统通知</strong> / <small>Edit...</div>
            </div>
            <hr>
            <form action="init.php?init=12" method="post" class="am-form am-form-horizontal form-actions">
                <div class="control-group">
                  <div class="controls hide" style="overflow: hidden; text-indent: -9999px;">
                      <input style="style="overflow: hidden; text-indent: -9999px;" "type="text" id="edit_id" name="edit_id" value="<?php echo $edit_message['id']; ?>">
                  </div>
                    <div class="am-form-group am-g-fixed">
                        <label for="edit_title" class="am-u-sm-3 am-form-label">标题 / System Session Title</label>
                        <div class="am-u-sm-9 am-form-group am-button-group">
                          <input type="text" id="edit_title" name="edit_title" placeholder="标题(可留空)" value="<?php echo $edit_message['post_title']; ?>">
                        </div>
                    </div>
                    <div class="am-form-group am-g-fixed">
                        <label for="edit_title" class="am-u-sm-3 am-form-label">修改通知内容 / System Session Cotent...</label>
                        <div class="am-u-sm-9 am-form-group am-button-group">
                          <input type="text" id="edit_message" name="edit_message" placeholder="系统通知内容" value="<?php echo $edit_message['post_content']; ?>">
                          <hr>
                          <button type="submit" class="am-btn am-radius am-btn-sm am-fl color-btn" type="submit" data-am-popover="{theme: 'warning', content: 'Believe yourself.', trigger: 'hover focus'}"><i class="am-icon-pencil"></i> 修改</button>&nbsp;
                          <a href="init.php?init=12" role="button" class="am-btn color-btn" data-am-popover="{content: '返回上一级', trigger: 'hover focus'}"><i class="am-icon-reply"></i> 返回</a>
                        </div>
                    </div>
                </div>
            </form>
            <?php
            }
        }
      if (isset($_GET['view']) == true) {
          $view_message = $oapost->view($_GET['view']);
          if ($view_message) {
              ?>
          <div class="color-card color-card-bordered">
              <div class="color-card-head">
                  <div class="color-card-head-title">查看</div>
              </div>
              <div class="color-card-body">
                <p><strong><?php echo $view_message['post_title']; ?></strong><em>&nbsp;<?php echo $view_message['post_date']; ?> - <?php $message_user = $moyunuser->view_user($view_message['post_user']); if($message_user){ echo '<a href="init.php?init=12&user='.$message_user['id'].'" target="_self">'.$message_user['user_name'].'</a>'; unset($message_user); } ?></em></p>
                <p>&nbsp;</p>
                <p>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $view_message['post_content']; ?></p>
                <p>&nbsp;</p>
                <p><a href="init.php?init=12" role="button" class="am-btn color-btn" data-am-popover="{content: '返回上一级', trigger: 'hover focus'}"><i class="am-icon-reply"></i> 返回</a></p>
              </div>
          </div>
            <?php
        }
    }
    ?>

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
    });
</script>
