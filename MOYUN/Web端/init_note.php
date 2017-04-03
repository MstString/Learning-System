<?php
/**
 * 笔记
 * @author string.zhengyang@gmail.com
 * @version 2
 * @package MOYUN
 */
/**
 * 页面引用判断
 * @since 1
 */
if (isset($init_page) == false) {
    die();
}

/**
 * 初始化基础变量
 * @since 1
 */
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$max = 10;
$sort = 0;
$desc = true;
$post_type = 'text';
$post_status = 'private';

/**
 * 提示消息变量
 * @since 1
 */
$message = '';
$message_bool = false;

/**
 * 添加新的记录
 * @since 1
 */
if (isset($_POST['add_title']) == true && isset($_POST['add_content']) == true) {
    if ($_POST['add_title'] && $_POST['add_content']) {
        if ($oapost->add($_POST['add_title'], $_POST['add_content'], 0, $post_type, 0, $post_user, null, null, null, $post_status, null)) {
            $message = '添加成功。';
            $message_bool = true;
        } else {
            $message = '无法添加笔记。';
            $message_bool = false;
        }
    } else {
        $message = '无法添加笔记，必须输入标题和内容。';
        $message_bool = false;
    }
}

/**
 * 修改记录信息
 * @since 1
 */
if (isset($_POST['edit_id']) == true && isset($_POST['edit_title']) == true && isset($_POST['edit_content']) == true) {
    if ($_POST['edit_title'] && $_POST['edit_content']) {
        $edit_view = $oapost->view($_POST['edit_id']);
        if ($edit_view) {
            if ($oapost->edit($_POST['edit_id'], $_POST['edit_title'], $_POST['edit_content'], $post_type, $edit_view['post_parent'], $edit_view['post_user'], $edit_view['post_password'], $edit_view['post_name'], $edit_view['post_url'], $edit_view['post_status'], $edit_view['post_meta']) == true) {
                $message = '修改成功！';
                $message_bool = true;
            } else {
                $message = '无法修改笔记。';
                $message_bool = false;
            }
        } else {
            $message = '无法修改笔记。';
            $message_bool = false;
        }
    } else {
        $message = '无法修改笔记，必须输入标题和内容。';
        $message_bool = false;
    }
}

/**
 * 删除记录
 * @since 1
 */
if (isset($_GET['del']) == true) {
    $del_view = $oapost->view($_GET['del']);
    if ($del_view) {
        //删除ID
        if ($oapost->del($del_view['id']) == true) {
            $message = '删除成功。';
            $message_bool = true;
        } else {
            $message = '无法删除该笔记。';
            $message_bool = false;
        }
    } else {
        $message = '无法删除该笔记。';
        $message_bool = false;
    }
}

/**
 * 获取消息列表记录数
 * @since 1
 */
$table_list_row = $oapost->view_list_row($post_user, null, null, $post_status, $post_type);

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
$table_list = $oapost->view_list($post_user, null, null, $post_status, $post_type, $page, $max, $sort, $desc);
?>
<!-- 管理表格 -->
<div class="admin-content">
    <div class="admin-content-body">

      <div class="am-cf am-padding">
        <div class="am-fl am-cf">
          <strong class="am-text-primary am-text-lg">学习笔记</strong> / <small>Learning ING...</small>
        </div>
      </div>

      <hr>

     <div class="am-g">
       <div class="am-u-sm-12">

         <form class="am-form">
           <table class="am-table am-table-bordered am-table-hover color-table-bordered">
           <thead>
           <tr>
             <th class="table-author"><span class="am-icon-file-o"></span> 笔记标题</th>
             <th class="am-hide-md-down"><span class="am-icon-calendar"></span> 创建日期</th>
             <th class="table-set"><span class="am-icon-gear"></span> 操作</th>
           </tr>
           </thead>
           <tbody id="message_list">
               <?php if ($table_list) {
                   foreach ($table_list as $v) { ?>
                       <tr>
                           <td><a href="<?php echo $page_url.'&view='.$v['id']; ?>" target="_self"><?php echo $v['post_title']; ?></a></td>
                           <td><?php echo $v['post_date']; ?></td>
                           <td>
                             <a href="<?php echo $page_url.'&view='.$v['id']; ?>#view"><div class="am-btn am-btn-secondary am-btn-xs am-radius"><i class="am-icon-search"></i> 详情</div></a>&nbsp;
                             <a href="<?php echo $page_url.'&edit='.$v['id']; ?>#edit"><div class="am-btn am-btn-success am-btn-xs am-radius"><i class="am-icon-pencil"></i> 编辑</div></a>&nbsp;
                             <a href="<?php echo $page_url.'&del='.$v['id']; ?>"><div class="am-btn am-btn-danger am-btn-xs am-radius"><i class="am-icon-trash"></i> 删除</div></a>
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
     <!-- 查看笔记详情 -->
     <?php if (isset($_GET['view']) == true) { $view_res = $oapost->view($_GET['view']); if($view_res){ ?>
       <div class="am-cf am-padding">
         <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">查看笔记</strong> / <small>看仔细了哦!</div>
       </div>
       <div class="am-u-sm-12">
        <div class="am-u-md-8 am-u-end color-margin-bottom">
       <div class="color-card color-card-bordered">
          <div class="color-card-head">
              <div class="color-card-head-title">笔记详情</div>
          </div>
          <div class="color-card-body">
            <p><?php echo $view_res['post_title']; ?> - <?php echo $view_res['post_date']; ?></p>
            <p>&nbsp;</p>
            <p><?php echo $view_res['post_content']; ?></p>
            <p>&nbsp;</p>
            <hr>
            <div>
            <a href="<?php echo $page_url.'&edit='.$view_res['id']; ?>#edit" role="button" class="am-btn color-btn" data-am-popover="{content: 'That is OK！', trigger: 'hover focus'}"><i class="am-icon-pencil"></i> 编辑</a>
            <a href="<?php echo $page_url; ?>" role="button" class="am-btn color-btn" data-am-popover="{content: '返回上一级', trigger: 'hover focus'}"><i class="am-icon-reply"></i> 返回</a>
            </div>
          </div>
      </div>
    </div>
  </div>
     <?php } } ?>

     <hr>
     <!-- 修改笔记的功能 -->
     <?php if (isset($_GET['view']) == false && isset($_GET['edit']) == false) { ?>
       <div class="am-cf am-padding">
         <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">书写笔记</strong> / <small>Write a note!</div>
       </div>

       <hr>

       <form class="am-form am-form-horizontal form-actions" action="<?php echo $page_url; ?>" method="post">
           <div class="am-form-group am-g-fixed">
               <label for="new_name" class="am-u-sm-3 am-form-label">笔记名称 / Note Name</label>
               <div class="am-u-sm-9 am-form-group am-button-group">
                 <input type="text" id="add_title" name="add_title" placeholder="笔记本名称">
                 <small>Your Note Name...</small>
               </div>
           </div>
           <div class="am-form-group am-g-fixed">
               <label for="new_message" class="am-u-sm-3 am-form-label">笔记内容 / Message</label>
               <div class="am-u-sm-9 am-form-group">
                 <textarea rows="5" id="add_content" name="add_content" placeholder="笔记内容..."></textarea>
                 <small>请镇重的写下你的笔记...</small>
                 <hr>
                   <a id="msg"></a>
                   <button type="submit" class="am-btn am-radius am-btn-sm am-fl color-btn" data-am-popover="{theme: 'warning', content: 'That OK!', trigger: 'hover focus'}">添加</button>
               </div>
           </div>
       </form>
     <?php } ?>

     <?php if (isset($_GET['edit']) == true && isset($_GET['view']) == false) {  $view_res = $oapost->view($_GET['edit']); if($view_res){ ?>
      <!-- 编辑 -->
      <div class="am-cf am-padding">
        <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">修改笔记</strong> / <small>Change a note!</div>
      </div>

      <hr>

      <form class="am-form am-form-horizontal form-actions" action="<?php echo $page_url.'&view='.$view_res['id']; ?>" method="post">
          <div class="am-form-group am-g-fixed">
              <label for="edit_title" class="am-u-sm-3 am-form-label">新的笔记名称 / New Note Name</label>
              <div class="am-u-sm-9 am-form-group am-button-group">
                <input type="text" id="edit_title" name="edit_title" placeholder="新的笔记本名称" value="<?php echo $view_res['post_title']; ?>">
                <small>Your New Note Name...</small>
              </div>
              <!-- 隐藏域 -->
              <div class="hidden" style="overflow: hidden; text-indent: -9999px;">
                  <input type="text" name="edit_id" value="<?php echo $view_res['id']; ?>">
              </div>
          </div>
          <div class="am-form-group am-g-fixed">
              <label for="edit_content" class="am-u-sm-3 am-form-label">新的计划 / New Plan</label>
              <div class="am-u-sm-9 am-form-group">
                <textarea rows="3" id="edit_content" name="edit_content" placeholder="请输入你的计划详情..."><?php echo $view_res['post_content']; ?></textarea>
                <small>请你把握好自己的时间...</small>
                <hr>
                  <button type="submit" class="am-btn am-radius am-btn-sm am-fl color-btn" type="submit" data-am-popover="{theme: 'warning', content: 'Believe yourself.', trigger: 'hover focus'}"><i class="am-icon-pencil"></i> 修改</button>&nbsp;
                  <a href="<?php echo $page_url.'&view='.$view_res['id']; ?>" role="button" class="am-btn color-btn" data-am-popover="{content: '返回上一级', trigger: 'hover focus'}"><i class="am-icon-reply"></i> 返回</a>
              </div>
          </div>
      </form>
      <?php } } ?>

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
