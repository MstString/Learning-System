<?php
/**
 * 个人成绩考评
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
$max = 30;
$sort = 0;
$desc = true;
$post_type = 'performance';
$post_status = 'private';
$post_parent = '';

/**
 * 提示消息变量
 * @since 1
 */
$message = '';
$message_bool = false;

/**
 * 获取列表记录数
 * @since 1
 */
$table_list_row = $oapost->view_list_row($post_user, null, null, $post_status, $post_type, $post_parent);

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
$table_list = $oapost->view_list($post_user, null, null, $post_status, $post_type, $page, $max, $sort, $desc, $post_parent);

/**
 * 计算业绩
 * @since 1
 */
$performance_count = $oapost->sum_fields('performance', $post_user, 'post_url');
$date_mouth_start = date('Y-m') . '-00 00:00:00';
$date_mouth_end = date('Y') . '-' . ((int) date('m') + 1) . '-00 00:00:00';
$performance_mouth_count = $oapost->sum_fields('performance', $post_user, 'post_url', $date_mouth_start, $date_mouth_end);
?>
<!-- 管理表格 -->
<div class="admin-content">
    <div class="admin-content-body">

      <div class="am-cf am-padding">
        <div class="am-fl am-cf">
          <strong class="am-text-primary am-text-lg">作业成绩</strong> / <small>Never Say Never...</small>
        </div>
      </div>

      <hr>

      <div class="am-g">
        <div class="am-u-sm-12">

          <div class="am-u-md-8 am-u-end color-margin-bottom">
          <div class="color-card color-card-bordered color-card-radius">
              <div class="color-card-head">
                  <div class="color-card-head-title">查看成绩</div>
              </div>
              <div class="color-card-body">
              <p>本学期总成绩：<?php echo $performance_count; ?></p>
              <p>本月作业成绩：<?php echo $performance_mouth_count; ?></p>
              </div>
          </div>
        </div>

        <hr>

        <form class="am-form">
          <table class="am-table am-table-bordered am-table-hover color-table-bordered">
          <thead>
          <tr>
            <th class="table-author"><span class="am-icon-file-o"></span> 作业名称</th>
            <th class="am-hide-md-down"><span class="am-icon-terminal"></span> 作业成绩</th>
            <th class="table-set"><span class="am-icon-gear"></span> 操作</th>
          </tr>
          </thead>
          <tbody id="message_list">
            <?php if ($table_list) {
                foreach ($table_list as $v) { ?>
                    <tr>
                        <td><?php echo $v['post_title']; ?></td>
                        <td><?php echo $v['post_url']; ?></td>
                        <td>
                            <div class="btn-group">
                                <a href="init.php?init=3&view=<?php echo $v['post_parent']; ?>" role="button" class="am-btn color-btn" data-am-popover="{theam: 'danger', content: 'O(∩_∩)O', trigger: 'hover focus'}"><i class="am-icon-search"></i> 查看详情</a>
                            </div>
                        </td>
                    </tr>
            <?php } } ?>
          </tbody>
        </table>
      </form>

      <div class="am-cf">
        <ul class="am-pagination am-default admin-content-pagination">
          <li class="am-<?php if ($page <= 1) { echo ' disabled'; } ?>" style="float:left; padding-left: 25px;"><a href="<?php echo $page_url . '&page=' . $page_prev; ?>">&laquo;上一页</a></li>
          <li class="am-<?php if ($page >= $page_max) { echo ' disabled'; } ?>" style="float:right; margin-right:25px;"><a href="<?php echo $page_url.'&page='.$page_next; ?>">下一页&raquo;</a></li>
        </ul>
      </div>

      </div>
    </div>
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
