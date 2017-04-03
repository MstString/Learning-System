<?php
/**
 * 通讯录页面
 * @author string.zhengyang@gmail.com
 * @version 2
 * @package MOYUN
 */
if (isset($init_page) == false) {
    die();
}

/**
 * 初始化变量
 * @since 5
 */
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$max = 20;
$sort = 0;
$desc = true;

/**
 * 操作消息内容
 * @since 1
 */
$message = '';
$message_bool = false;

/**
 * 添加新的联系人
 * @since 1
 */
if (isset($_POST['edit_id']) == false && isset($_POST['new_title']) == true) {
    $post_name = null;
    if (isset($_POST['new_name']) == true) {
        if ($_POST['new_name'] == '') {
            $post_name = '';
        } else {
            $post_user_view = $moyunuser->view_user_name($_POST['new_name']);
            if ($post_user_view) {
                if ($post_user_view['id'] != $post_user) {
                    $post_name = $post_user_view['id'];
                } else {
                    $message = '不能添加当前用户自身！';
                    $message_bool = false;
                }
            } else {
                $message = '该用户不存在！';
                $message_bool = false;
            }
        }
    }
    if (!$message) {
        if ($oapost->add($_POST['new_title'], '', 0, 'addressbook', 0, $post_user, null, $post_name, null, 'public', null)) {
            $message = '添加联系人成功！';
            $message_bool = true;
        } else {
            $message = '无法添加新的联系人。';
            $message_bool = false;
        }
    }
}

/**
 * 添加新的联系人信息
 * @since 1
 */
if (isset($_POST['edit_id']) == true && isset($_POST['new_title']) == true && isset($_POST['new_content']) == true) {
    if ($_POST['new_title'] && $_POST['new_content']) {
        if ($oapost->add($_POST['new_title'], $_POST['new_content'], 'addressbook', $_POST['edit_id'], $post_user, null, null, null, 'public', null)) {
            $message = '';
        } else {
            $message = '无法添加新的联系信息。';
            $message_bool = false;
        }
    }
}

/**
 * 编辑联系人或子信息
 * @since 1
 */
if (isset($_POST['edit_id']) == true && isset($_POST['edit_title']) == true) {
    $post_content = '';
    if (isset($_POST['edit_content']) == true) {
        $_POST['edit_content'] = $post_content;
    }
    $post_parent = 0;
    if (isset($_POST['edit_parent']) == true) {
        $_POST['edit_parent'] = $post_parent;
    }
    $post_name = null;
    if (isset($_POST['edit_name']) == true) {
        if ($_POST['edit_name'] == '') {
            $post_name = '';
        } else {
            $post_user_view = $moyunuser->view_user_name($_POST['edit_name']);
            if ($post_user_view) {
                if ($post_user_view['id'] != $post_user) {
                    $post_name = $post_user_view['id'];
                } else {
                    $message = '不能改为当前用户！';
                    $message_bool = false;
                }
            } else {
                $message = '该用户不存在！';
                $message_bool = false;
            }
        }
    }
    if (!$message) {
        if ($oapost->edit($_POST['edit_id'], $_POST['edit_title'], $post_content, 'addressbook', $post_parent, $post_user, null, $post_name, null, 'public', null)) {
            $message = '编辑联系人信息成功！';
            $message_bool = true;
        } else {
            $message = '无法编辑联系人信息，请稍候重试。';
            $message_bool = false;
        }
    }
}

/**
 * 删除联系人或子信息
 * @since 1
 */
if (isset($_GET['del']) == true) {
    $del_view = $oapost->view($_GET['del']);
    if ($del_view) {
        if ($del_view['post_user'] == $post_user) {
            if ($oapost->del_parent($del_view['id'])) {
                $message = '删除联系人成功！';
                $message_bool = true;
            } else {
                $message = '无法删除该联系人。';
                $message_bool = false;
            }
        }
    }
}

/**
 * 获取消息列表记录数
 * @since 1
 */
$table_list_row = $oapost->view_list_row($post_user, null, null, 'public', 'addressbook', 0);

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
 * 获取消息列表
 * @since 1
 */
$table_list = $oapost->view_list($post_user, null, null, 'public', 'addressbook', $page, $max, $sort, $desc, 0);
?>
<!-- 管理表格 -->
<div class="admin-content">
    <div class="admin-content-body">

      <div class="am-cf am-padding">
        <div class="am-fl am-cf">
          <strong class="am-text-primary am-text-lg">我的好友</strong> / <small>My Friends...</small>
        </div>
      </div>

      <hr>

     <div class="am-g">
       <div class="am-u-sm-12">

         <form class="am-form">
           <table class="am-table am-table-bordered am-table-hover color-table-bordered">
           <thead>
           <tr>
             <th class="table-author"><span class="am-icon-file-o"></span> 好友姓名</th>
             <th class="table-set"><span class="am-icon-gear"></span> 操作</th>
           </tr>
           </thead>
             <tbody id="message_list">
              <?php if ($table_list) {
                  foreach ($table_list as $v) { ?>
                      <tr>
                          <td><a href="<?php echo $page_url.'&view='.$v['id']; ?>" target="_self"><?php echo $v['post_title']; ?></a></td>
                          <td>
                            <a href="<?php echo $page_url.'&view='.$v['id']; ?>#view"><div class="am-btn am-btn-secondary am-btn-xs am-radius"><i class="am-icon-search"></i> 详情</div></a>&nbsp;
                            <?php if($v['post_name']){ ?>
                            <a href="init.php?init=1&user=<?php echo $v['post_name']; ?>#send"><div class="am-btn am-btn-success am-btn-xs am-radius"><i class="am-icon-envelope"></i> 发送消息</div></a>&nbsp
                            <?php } ?>
                            <a href="<?php echo $page_url.'&edit='.$v['id']; ?>#edit"><div class="am-btn am-btn-warning am-btn-xs am-radius"><i class="am-icon-pencil"></i> 编辑</div></a>
                            <a href="<?php echo $page_url.'&del='.$v['id']; ?>"><div class="am-btn am-btn-danger am-btn-xs am-radius"><i class="am-icon-trash"></i> 删除</div></a>
                          </td>
                      </tr>
                  <?php }
              } ?>
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

   <!-- 添加新的联系人 -->
   <?php if (isset($_GET['edit']) == false && isset($_GET['view']) == false) { ?>
     <div class="am-cf am-padding">
       <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">添加好友</strong> / <small>多多益善</div>
     </div>
     <hr>
     <form class="am-form am-form-horizontal form-actions" action="<?php echo $page_url; ?>" method="post">
         <div class="am-form-group am-g-fixed">
             <label for="new_title" class="am-u-sm-3 am-form-label">好友学号 / Friend ID</label>
             <div class="am-u-sm-9 am-form-group am-button-group">
               <input type="text" id="new_title" name="new_title" placeholder="好友学号">
               <small>Your friend ID...</small>
               <hr>
               <a id="msg"></a>
               <button type="submit" class="am-btn am-radius am-btn-sm am-fl color-btn" data-am-popover="{theme: 'warning', content: '不亦说乎', trigger: 'hover focus'}">添加</button>
             </div>
         </div>
         <!-- <div class="am-form-group am-g-fixed">
             <label for="new_name" class="am-u-sm-3 am-form-label">好友昵称 / 你懂的</label>
             <div class="am-u-sm-9 am-form-group">
               <input type="text" id="new_name" name="new_name" placeholder="好友昵称">
               <small>注意是昵称哦...</small>
             </div>
         </div> -->
     </form>
   <?php }
    if (isset($_GET['edit']) == true && isset($_GET['view']) == false) {
        $edit_res = $oapost->view($_GET['edit']);
        if ($edit_res) {
            $edit_user = null;
            if($edit_res['post_name']){
                $edit_user = $moyunuser->view_user($edit_res['post_name']);
            }
        $edit_childrens = $oapost->view_list($post_user, null, null, 'public', 'addressbook', 1, 9999, 0, false, $edit_res['id']);
    ?>
    <!-- 编辑联系人 -->
    <div class="am-cf am-padding">
      <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">编辑好友</strong> / <small>求同存异</div>
    </div>
    <hr>
    <form action="<?php echo $page_url.'&view='.$edit_res['id']; ?>" method="post" class="am-form am-form-horizontal form-actions">
      <div class="am-form-group am-g-fixed">
          <label for="edit_title" class="am-u-sm-3 am-form-label">好友真实姓名 / Really name</label>
          <div class="am-u-sm-9 am-form-group am-button-group">
            <input type="text" id="edit_title" name="edit_title" placeholder="好友姓名" value="<?php echo htmlentities($edit_res['post_title']); ?>">
            <small>Your Note Name...</small>
          </div>
          <div class="hidden" style="overflow: hidden; text-indent: -9999px;">
            <input type="text" id="edit_id" name="edit_id" value="<?php echo $edit_res['id']; ?>">
          </div>
          <label for="edit_name" class="am-u-sm-3 am-form-label">好友系统ID / ID</label>
          <div class="am-u-sm-9 am-form-group am-button-group">
            <input type="text" id="edit_name" name="edit_name" placeholder="好友新的用户名/(ㄒoㄒ)/~~" value="<?php if($edit_user){ echo $edit_user['user_username']; } ?>">
            <small>Nick Name...</small>
          </div>
          <?php if($edit_childrens){ foreach($edit_childrens as $v){ ?>
            <!-- <label class="am-u-sm-3 am-form-label" for="edit_x_<?php echo $v['id']; ?>"><?php echo htmlentities($v['post_title']); ?></label> -->
            <label class="am-u-sm-3 am-form-label" for="edit_x_<?php echo $v['id']; ?>"><?php echo $v['post_title']; ?></label>
            <div class="am-u-sm-9 am-form-group am-button-group">
                    <input type="text" id="edit_x_<?php echo $v['id']; ?>" name="edit_x_<?php echo $v['id']; ?>" placeholder="<?php echo $v['post_title']; ?>" value="<?php $v_c = $oapost->view($v['id']); if($v_c){ echo htmlentities($v_c['post_content']); unset($v_c); } ?>">
                    <hr>
                    <a href="<?php echo $page_url.'&edit='.$edit_res['id'].'&del='.$v['id'].'#edit'; ?>"><div class="am-btn am-btn-xs am-radius am-btn-sm am-fl color-btn" data-am-popover="{theme: 'warning', content: 'Are you sure?', trigger: 'hover focus'}"><span class="am-icon-trash"></span> 删除</div></a>
            </div>
            <?php } } ?>
            <label class="am-u-sm-3 am-form-label" for="new_title">通讯方式</label>
            <div class="am-u-sm-9 am-form-group am-button-group">
                    <input type="text" id="new_title" name="new_title" placeholder="QQ 微信号 or 手机号等" value="">
            </div>
            <label class="am-u-sm-3 am-form-label" for="new_title">线下联系方式</label>
            <div class="am-u-sm-9 am-form-group am-button-group">
                  <input type="text" id="new_content" name="new_content" placeholder="联系方式" value="">
                  <hr>
                  <a id="msg"></a>
                  <button type="submit" class="am-btn am-radius am-btn-sm am-fl color-btn" type="submit" data-am-popover="{theme: 'warning', content: 'Change it.', trigger: 'hover focus'}">修改 </button>&nbsp
                  <a href="<?php echo $page_url; ?>" role="button" class="am-btn color-btn" data-am-popover="{content: 'Are you sure ?', trigger: 'hover focus'}">取消</a>
            </div>

      </div>
    </form>
    <?php
        }
    }
    if (isset($_GET['view']) == true) {
        $view_res = $oapost->view($_GET['view']);
        if ($view_res) {
            $view_user = null;
            if($view_res['post_name']){
                $view_user = $moyunuser->view_user($view_res['post_name']);
            }
            $view_childrens = $oapost->view_list($post_user, null, null, 'public', 'addressbook', 1, 9999, 0, false, $view_res['id']);
            ?>
            <!-- 查看好友信息详情 -->
            <div class="am-cf am-padding">
              <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">查看好友详情</strong> / <small>八卦之心</div>
            </div>
            <hr>
            <div class="am-u-sm-12">
            <div class="am-u-md-8 am-u-end color-margin-bottom">
            <div class="color-card color-card-bordered">
                <div class="color-card-head">
                    <div class="color-card-head-title">好友详情</div>
                </div>
                <div class="color-card-body">
                  <p><b>姓名：</b>&nbsp;&nbsp;<?php echo $view_res['post_title']; ?></p>
                  <!-- <p><b>所属用户</b>&nbsp;&nbsp;<?php if($view_user){ echo $view_user['user_username']; }else{ echo '无'; } ?></p> -->
                  <?php if($view_childrens){ foreach($view_childrens as $k=>$v){ ?>
                  <p><b><?php echo $v['post_title']; ?>：</b>&nbsp;&nbsp;<?php $v_c = $oapost->view($v['id']); if($v_c){ echo $v_c['post_content']; unset($v_c); } ?></p>
                  <?php } }else{ ?>
                  <p>There is nothing!</p>
                  <?php } ?>
                  <div>
                  <a href="<?php echo $page_url; ?>&edit=<?php echo $view_res['id']; ?>#edit" role="button" class="am-btn color-btn" data-am-popover="{theme:'warning', content: 'That is OK！', trigger: 'hover focus'}"><i class="am-icon-pencil"></i> 编辑</a>
                  <a href="<?php echo $page_url; ?>" role="button" class="am-btn color-btn" data-am-popover="{theme:'warning', content: '返回上一级', trigger: 'hover focus'}"><i class="am-icon-reply"></i> 返回</a>
                  </div>
                </div>
              </div>
            </div>
            </div>
            <?php
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
    $(document).ready(function() {
        var message = "<?php echo $message; ?>";
        var message_bool = "<?php echo $message_bool ? '2' : '1'; ?>";
        if (message != "") {
            msg(message_bool, message, message);
        }
    });
</script>
