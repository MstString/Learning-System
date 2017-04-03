<?php
/**
 * 公告留言界面
 * @author string.zhengyang@gmail.com
 * @version 2
 * @package MOYUN
 */
if (isset($init_page) == false) {
    die();
}

/**
 * 初始化变量
 * @since 2
 */
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$max = 10;
$sort = 0;
$desc = true;

/**
 * 操作消息内容
 * @since 1
 */
$message = '';
$message_bool = false;

/**
 * 初始化post_parent
 * @since 1
 */
$post_parent = 0;
if (isset($_GET['parent']) == true) {
    if ($_GET['parent'] > 0) {
        $new_parent_view = $oapost->view($_GET['parent']);
        if ($new_parent_view) {
            if ($new_parent_view['post_type'] == 'messageboard') {
                $post_parent = $new_parent_view['id'];
            }
        } else {
            $message = '您回复的留言不存在！';
            $message_bool = false;
        }
    }
}

/**
 * 添加新的留言
 * @since 1
 */
if (isset($_POST['new_content']) == true) {
    $str_len = strlen($_POST['new_content']);
    if ($str_len > 0 && $str_len < 500) {
        if ($oapost->add('', $_POST['new_content'], 0, 'messageboard', $post_parent, $post_user, null, null, null, 'public', null)) {
            $message = '留言发布成功！';
            $message_bool = true;
        } else {
            $message = '无法发表该留言。';
            $message_bool = false;
        }
    } else {
        $message = '留言不能为空，或不能超过500字。';
        $message_bool = false;
    }
}

/**
 * 删除留言
 * @since 1
 */
if ((isset($_GET['del']) == true && $logged_admin == true) || (isset($_GET['del']) == true && $logged_admin == true)) {
    if ($oapost->del($_GET['del'])) {
        $message = '删除留言成功！';
        $message_bool = true;
    } else {
        $message = '无法删除该留言。';
        $message_bool = false;
    }
}

/**
 * 获取列表记录数
 * @since 1
 */
$table_list_row = $oapost->view_list_row(null, null, null, 'public', 'messageboard', 0);

/**
 * 计算页码
 * @since 1
 */
$page_max = ceil($table_list_row / $max);
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
 * 获取列表
 * @since 1
 */
$message_list = $oapost->view_list(null, null, null, 'public', 'messageboard', $page, $max, $sort, $desc, 0);

/**
 * 留言回复递归
 * @since 1
 */
$echo_parent = '';
function view_parent($id){
    global $logged_admin,$page_url,$echo_parent,$oapost,$moyunuser;
    $view_p = $oapost->view_list(null, null, null, 'public', 'messageboard', 1, 9999, 0, true, $id);
    if($view_p){
        foreach($view_p as $vr){
            $echo_parent .= '<p><a href="'.$page_url.'&parent='.$vr['id'].'"></a><p><h3>';
            $v_user = $moyunuser->view_user($vr['post_user']);
            $v_user_name = '';
            if($v_user){
                $v_user_name = $v_user['user_username'];
            }
            $echo_parent .= $v_user_name.'</h3>';
            unset($v_user,$v_user_name);
            $v_view = $oapost->view($vr['id']);
            $v_content = '';
            if($v_view){
                $v_content = $v_view['post_content'];
            }
            $echo_parent .= $v_content.'&nbsp;<span><br><a href="'.$page_url.'&parent='.$vr['id'].'#nmd" target="_self">回复</a>';

                $echo_parent .= '&nbsp;<a href="'.$page_url.'&del='.$vr['id'].'" target="_self">删除</a>';

            unset($v_view,$v_content);
            $echo_parent .= '</p>';
            view_parent($vr['id']);
            $echo_parent .= '</p></p><hr>';
        }
    }
    unset($view_p);
}
?>
<!-- 管理表格 -->
<div class="admin-content">
    <div class="admin-content-body">
      <div class="am-cf am-padding">
        <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">公告留言</strong> / <small>Message Border</div>
      </div>
      <hr>
      <div class="am-g">
        <div class="am-u-sm-6">
          <?php if($message_list){ foreach($message_list as $v){ ?>
            <div class="am-panel am-panel-default admin-sidebar-panel">
              <div class="am-panel-bd">
                <p><span class="am-icon-bookmark"></span> 公告</p>
                <hr>
                <h3><?php $v_user = $moyunuser->view_user($v['post_user']); if($v_user){ echo $v_user['user_username']; unset($v_user); } ?></h3>

                <p><?php $v_view = $oapost->view($v['id']); if($v_view){ echo $v_view['post_content']; unset($v_view); } ?>&nbsp;<?php if ($logged_admin == true) { ?><span><br><a href="<?php echo $page_url.'&parent='.$v['id']; ?>#nmd" target="_self"> 回复</a>
                <?php } ?>
                <?php
                    if ($logged_admin == true) {
                    echo '&nbsp;<a href="'.$page_url.'&del='.$v['id'].'" target="_self">删除</a></span>';
                    }

                // 调用回复函数
                view_parent($v['id']);
                echo $echo_parent;
                unset($echo_parent);
                ?>
                </p>
              </div>
            </div>

          <?php } }?>
        </div>
      </div>

      <br>

      <!-- 翻页 -->
      <div class="am-cf">
        <ul class="am-pagination am-default admin-content-pagination" style="padding-left: 25px;">
          <li class="am-<?php if($page<=1){ echo ' disabled'; } ?>" style="float:left;"><a href="<?php echo $page_url.'&page='.$page_prev; ?>">&laquo;上一页</a></li>
          <li class="am-<?php if($page>=$page_max){ echo ' disabled'; } ?>" style="float:right; margin-right:25px;"><a href="<?php echo $page_url.'&page='.$page_next;?>">下一页&raquo;</a></li>
        </ul>
      </div>

      <?php if ($logged_admin == true) { ?>
      <hr>
      <div class="am-cf am-padding">
        <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">公共留言板</strong> / <small>白首不分离</div>
      </div>

      <hr>

      <form class="am-form am-form-horizontal form-actions" action="<?php echo $page_url . '&parent=' . $post_parent; ?>" method="post">
        <div class="am-form-group am-g-fixed">
            <label for="new_content" class="am-u-sm-3 am-form-label">公共留言 / Messages </label>
            <div class="am-u-sm-9 am-form-group">
              <textarea rows="5" id="new_content" name="new_content" placeholder="<?php if($post_parent>0){ echo '回复ID:'.$post_parent.''; } ?>"></textarea>
              <small>请写下你的留言...</small>
              <hr>
                <a id="msg"></a>
                <button type="submit" class="am-btn am-radius am-btn-sm am-fl color-btn" data-am-popover="{theme: 'warning', content: 'That OK!', trigger: 'hover focus'}">回复</button>&nbsp;
                <?php if($post_parent>0){ ?><a href="<?php echo $page_url; ?>&parent=0#nmd"  target="_self" role="button" class="am-btn color-btn" data-am-popover="{theme:'warning', content: '取消留言', trigger: 'hover focus'}"> 取消</a><?php } ?>
            </div>
        </div>
      </form>
      <?php } ?>
  </div>
  <footer class="admin-content-footer">
        <hr>
        <p class="am-padding-left">© 2016 MOYUN Team.</p>
   </footer>
</div>



<!-- Javascript -->
<script>
    $(document).ready(function() {
        var message = "<?php echo $message; ?>";
        var message_bool = "<?php echo $message_bool ? '2' : '1'; ?>";
        if (message != "") {
            msg(message_bool, message, message);
        }
        $("div[class='media'] > div[class='media-body'] > span,li[class='media well'] > div[class='media-body'] > span").hide();
        $("div[class='media'],li[class='media well']").hover(function(){
            $(this).children("div[class='media-body']").children('span').show();
        },function(){
            $(this).children("div[class='media-body']").children('span').hide();
        });
    });
</script>
