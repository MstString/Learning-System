<?php
/**
 * 个人短消息中心
 * @author string.zhengyang@gmail.com
 * @version 2
 * @package MOYUN
 */
if (isset($init_page) == false) {
    die();
}

/**
 * 初始化变量
 * @since 3
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
 * 添加新的消息
 * @since 1
 */
if (isset($_POST['new_message']) == true && isset($_POST['new_name']) == true) {
    $title = '';
    if (isset($_POST['new_title']) == true) {
        $title = $_POST['new_title'];
    } else {
        //引入截取字符串模块
        require(DIR_LIB . DS . 'plug-substrutf8.php');
        $title = plugsubstrutf8($_POST['new_message'], 100);
    }
    $new_user_view = $moyunuser->view_user_name($_POST['new_name']);
    if ($new_user_view) {
        if ($oapost->add($title, $_POST['new_message'], 0, 'message', 0, $post_user, null, $new_user_view['id'], null, 'private', null)) {
            $message = '消息成功发送！';
            $message_bool = true;
        } else {
            $message = '无法发送消息。';
            $message_bool = false;
        }
    } else {
        $message = '该用户不存在！';
        $message_bool = false;
    }
}

/**
 * 删除消息
 * @since 3
 */
if (isset($_GET['del']) == true) {
    $del_view = $oapost->view($_GET['del']);
    if ($del_view) {
        if ($del_view['post_status'] == 'private' && ($del_view['post_user'] == $post_user || $del_view['post_name'] == $post_user)) {
            if ($oapost->del($_GET['del'])) {
                $message = '删除消息成功！';
                $message_bool = true;
            } else {
                $message = '无法删除该消息，删除失败。';
                $message_bool = false;
            }
        } else {
            $message = '无法删除该消息，该消息不存在。';
            $message_bool = false;
        }
    } else {
        $message = '无法删除该消息，该消息不存在。';
        $message_bool = false;
    }
}

/**
 * 获取消息列表记录数
 * @since 3
 */
$message_list_row = $oapost->view_list_row(null, null, null, 'private', 'message',null,$post_user);

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
 * @since 3
 */
$message_list = $oapost->view_list(null, null, null, 'private', 'message', $page, $max, $sort, $desc, null, $post_user);
?>

<!-- 主界面 -->
<div class="admin-content">
    <div class="admin-content-body">

    <div class="am-cf am-padding">
      <div class="am-fl am-cf">
        <strong class="am-text-primary am-text-lg">个人消息管理中心</strong> / <small>快来整理你的信息吧！</small>
      </div>
    </div>

    <hr>

    <div class="am-g">
      <div class="am-u-sm-12">
        <form class="am-form">
          <table class="am-table am-table-bordered am-table-hover color-table-bordered">
          <thead>
          <tr>
            <th class="table-date  am-hide-md-down"><span class="am-icon-calendar"></span> 时间</th><th class="table-author"><span class="am-icon-user"></span> 姓名</th><th class="table-set"><span class="am-icon-gear"></span> 操作</th>
          </tr>
          </thead>
        <tbody>
          <?php if($message_list){ foreach($message_list as $v){ ?>
          <tr>
            <td class=" am-hide-md-down"><?php echo $v['post_date']; ?></td>
            <td><?php $message_user = $moyunuser->view_user($v['post_user']); if($message_user){ echo '<a href="'.$page_url.'&user='.$message_user['id'].'" target="_self">'.$message_user['user_name'].'</a>'; unset($message_user); } ?></td>
            <!-- <td><a href="<?php echo $page_url.'&view='.$v['id']; ?>" target="_self"><?php echo $v['post_title']; ?></a></td> -->
            <td>
              <div class="am-btn-toolbar">
                  <a href="<?php echo $page_url.'&view='.$v['id']; ?>" target="_self"><div class="am-btn am-btn-secondary am-btn-xs am-radius"><i class="am-icon-search"></i> 详情</div></a>&nbsp;
                  <a href="<?php echo $page_url.'&user='.$v['post_user']; ?>" target="_self"><div class="am-btn am-btn-warning am-btn-xs am-radius"><i class="am-icon-envelope-o"></i> 回复</div></a>&nbsp;
                  <a href="<?php echo $page_url.'&del='.$v['id']; ?>" target="_self"><div class="am-btn am-btn-danger am-btn-xs am-radiusr am-hide-md-down"><i class="am-icon-trash-o"></i> 删除</div></a>
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
      if (isset($_GET['view']) == false) {
          $send_user = '';
          if(isset($_GET['user']) == true){
              $send_user_view = $moyunuser->view_user((int)$_GET['user']);
              if($send_user_view){
                  $send_user = $send_user_view['user_username'];
              }
          }
      ?>
      <!-- 发布消息 -->
      <div class="am-cf am-padding">
        <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">发送消息</strong> / <small>Send Message to you Love...</div>
      </div>
      <hr>
      <form action="<?php echo $page_url; ?>" method="post" class="am-form am-form-horizontal form-actions">
          <div class="am-form-group am-g-fixed">
              <label for="new_name" class="am-u-sm-3 am-form-label">我的好友 / ID</label>
              <div class="am-u-sm-9 am-form-group am-button-group">
                <input type="text" id="new_name" name="new_name" placeholder="我的好友 / ID" value="<?php echo $send_user; ?>">
                <small>What's your love's name?</small>
                <hr>
                <a href="init.php?init=6"><div class="am-btn am-btn-xs am-radius am-btn-sm am-fl color-btn" data-am-popover="{theme: 'warning', content: 'There is your friends.', trigger: 'hover focus'}"><span class="am-icon-users"></span> 朋友圈</div></a>
              </div>
          </div>
          <div class="am-form-group am-g-fixed">
              <label for="new_message" class="am-u-sm-3 am-form-label">消息内容 / Message</label>
              <div class="am-u-sm-9 am-form-group">
                <textarea rows="5" id="new_message" name="new_message" placeholder="消息内容..."></textarea>
                <small>请镇重的写下你要发送的消息...</small>
                <hr>
                  <a id="msg"><a>
                  <button type="submit" class="am-btn am-radius am-btn-sm am-fl color-btn" type="submit" data-am-popover="{theme: 'warning', content: 'To your Love.', trigger: 'hover focus'}">发送</button>
                </table>
              </div>
          </div>
      </form>
      <?php
      }
      if (isset($_GET['view']) == true) {
          $view_message = $oapost->view($_GET['view']);
          if ($view_message) {
              if($view_message['post_name'] == $post_user){
              ?>
              <!-- 查看消息详情 -->
              <div class="am-cf am-padding form-actions" id="view">
                <div class="am-cf am-padding">
                  <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">消息详情</strong> / <small>Send Message to you Love...</small></div>
                </div>
                <hr>
                <div class="am-comment-main am-g-fixed">
                <header class="am-comment-hd am-g-fixed">
                      <div class="am-comment-meta"><a href="#" class="am-comment-author"><?php $message_user = $moyunuser->view_user($view_message['post_user']); if($message_user){ echo '<a href="'.$page_url.'&user='.$message_user['id'].'" target="_self">'.$message_user['user_name'].'</a>'; unset($message_user); } ?> </a> 私信你于 <time><?php echo $view_message['post_date']; ?></time></div>
                </header>
                <div class="am-comment-bd"><p><?php echo $view_message['post_content']; ?></p></div>
                <hr>
                <div class="am-comment-bd am-fr">
                  <a href="init.php?init=1&user=<?php echo $v['post_name']; ?>#send" role="button" class="am-btn-success"><div class="am-btn am-btn-default am-btn-xs am-radius"><span class="am-icon-envelope-o"></span> 回复</div></a>
                  <a href="<?php echo $page_url; ?>" role="button" class="am-btn-danger"><div class="am-btn am-btn-danger am-btn-xs am-radius"><span class="am-icon-reply"></span> 返回</div></a>
                </div>
              </div>
              </div>

              <?php
              }
          }
      }
      ?>
    </div>
    <footer class="admin-content-footer">
          <hr>
          <p class="am-padding-left">© 2016 MOYUN Team.</p>
     </footer>
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
