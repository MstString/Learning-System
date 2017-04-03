<?php

/**
 * 文件分享中心
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
$post_type = 'file';
$post_status = 'public';

/**
 * 提示消息变量
 * @since 1
 */
$message = '';
$message_bool = false;

/**
 * 分享文件
 * @since 1
 */
if (isset($_GET['share']) == true) {
    $edit_view = $oapost->view($_GET['share']);
    if ($edit_view) {
        $edit_post_status = '';
        if ($edit_view['post_status'] == 'public') {
            $edit_post_status = 'private';
        } else {
            $edit_post_status = 'public';
        }
        if ($oapost->edit($edit_view['id'], $edit_view['post_title'], $edit_view['post_content'], $post_type, $edit_view['post_parent'], $edit_view['post_user'], $edit_view['post_password'], $edit_view['post_name'], $edit_view['post_url'], $edit_post_status, $edit_view['post_meta']) == true) {
            $message = '修改成功！';
            $message_bool = true;
        } else {
            $message = '无法修改文件信息。';
            $message_bool = false;
        }
    } else {
        $message = '无法修改文件信息';
        $message_bool = false;
    }
}

/**
 * 获取消息列表记录数
 * @since 1
 */
$table_list_row = $oapost->view_list_row(null, null, null, $post_status, $post_type, '');

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
$table_list = $oapost->view_list(null, null, null, $post_status, $post_type, $page, $max, $sort, $desc, '');
?>
<!-- 管理表格 -->
<div class="admin-content">
    <div class="admin-content-body">

      <div class="am-cf am-padding">
        <div class="am-fl am-cf">
          <strong class="am-text-primary am-text-lg">笔记共享中心</strong> / <small>Note Share...</small>
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
              <th class="am-hide-md-down"><span class="am-icon-calendar"></span> 上传时间</th>
              <th class="am-hide-md-down"><span class="am-icon-user"></span> 上传用户</th>
              <th class="table-set"><span class="am-icon-gear"></span> 操作</th>
            </tr>
            </thead>
            <tbody id="message_list">
              <?php if ($table_list) {
                foreach ($table_list as $v) { ?>
              <tr>
                  <td><a href="<?php echo $page_url.'&view='.$v['id']; ?>" target="_self"><?php echo $v['post_title']; ?></a></td>
                  <td class="am-hide-md-down"><?php echo $v['post_date']; ?></td>
                  <td class="am-hide-md-down"><?php $v_user = $moyunuser->view_user($v['post_user']); if($v_user){ echo $v_user['user_name']; } ?></td>
                  <td>
                    <div class="btn-group">
                    <a href="file_download.php?id=<?php echo $v['id']; ?>" target="_blank"><div class="am-btn am-btn-success am-btn-xs am-radius"><i class="am-icon-download"></i> 下载</div></a>&nbsp;
                    <a href="<?php echo $page_url.'&view='.$v['id']; ?>#view"><div class="am-btn am-btn-success am-btn-xs am-radius"><i class="am-icon-search"></i> 查看</div></a>&nbsp;
                    <?php if($logged_admin || $logged_teacher){ ?><a href="<?php echo $page_url.'&share='.$v['id']; ?>"><div class="am-btn am-btn-success am-btn-xs am-radius  am-hide-md-down"><i class="am-icon-lock"></i> 取消分享</div></a>
                    <?php } ?>
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

    <?php if (isset($_GET['view']) == true) { $view_res = $oapost->view($_GET['view']); if($view_res){ ?>
      <div class="am-u-md-8 am-u-end color-margin-bottom">

      <div class="am-cf am-padding">
        <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">查看共享笔记</strong> / <small>相互尊重</div>
      </div>
      <div class="color-card color-card-bordered">
          <div class="color-card-head">
              <div class="color-card-head-title">笔记详情</div>
          </div>
          <div class="color-card-body">
            <dl class="dl-horizontal">
              <dt>文件名称：</dt>
              <dd><?php echo $view_res['post_title']; ?></dd>
              <dt>上传时间：</dt>
              <dd><?php echo $view_res['post_date']; ?></dd>
              <dt>上传用户：</dt>
              <dd><?php $view_user = $moyunuser->view_user($view_res['post_user']); if($view_user){ echo $view_user['user_name']; } ?></dd>
              <dt>文件描述：</dt>
              <dd><?php echo $view_res['post_content']; ?></dd>
          </dl>
          </div>
    </div>
  </div>
    <?php } } ?>
  </div>
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
