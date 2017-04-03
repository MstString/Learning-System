<?php
/**
 * 作业中心
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
 $post_status = isset($_GET['status']) ? $_GET['status'] : 'private';

 /**
  * 提示消息变量
  * @since 1
  */
 $message = '';
 $message_bool = false;

 /**
  * 上传新的文件
  * @since 1
  */
 /**
  * 引入文件处理类
  * @since 1
  */
 require(DIR_LIB . DS . 'core-file.php');

 /**
  * 处理上传
  * @since 1
  */
 $upload_post_name = 'add_uploadhomework';
 if (isset($_FILES[$upload_post_name]) == true) {
     if ($_FILES[$upload_post_name]['error'] == 0) {
         $config_uploadfile_on = $oaconfig->load('UPLOADFILE_ON');
         if ($config_uploadfile_on > 0) {
             $config_uploadfile_min = $oaconfig->load('UPLOADFILE_SIZE_MIN');
             $config_uploadfile_max = $oaconfig->load('UPLOADFILE_SIZE_MAX');
             $file_size = $_FILES[$upload_post_name]['size'] / 1024;
             if ($file_size > $config_uploadfile_min && $file_size < $config_uploadfile_max) {
                 //判断文件类型是否正确
                 $config_uploadfile_hibit_type = $oaconfig->load('UPLOADFILE_INHIBIT_TYPE');
                 $config_uploadfile_hibit_type_arr = null;
                 if ($config_uploadfile_hibit_type) {
                     $config_uploadfile_hibit_type_arr = explode(',', $config_uploadfile_hibit_type);
                 }
                 unset($config_uploadfile_hibit_type);
                 // 得到扩展名
                 $file_type = substr(strrchr($_FILES[$upload_post_name]['name'], '.'), 1);
                 if (in_array($file_type, $config_uploadfile_hibit_type_arr) == false || $config_uploadfile_hibit_type_arr == null) {
                     $post_file_sha1 = sha1_file($_FILES[$upload_post_name]['tmp_name']);
                     $upload_view = $oapost->view_list(null, null, null, 'public', $post_type, 1, 1, 0, false, 0, '', $post_file_sha1);
                     $upload_id = 0;
                     $upload_name = $_FILES[$upload_post_name]['name'];
                     // $upload_name = date("YmdHis").rand(100, 200).".".$file_type;
                     if ($upload_view) {
                         //如果文件已经存在，则直接引用
                         $message = '该文件已经存在！';
                         $message_bool = false;
                         // $upload_id = $upload_view[0]['id'];
                         // $upload_name = $upload_view[0]['post_name'];
                     } else {
                         //如果文件不存在，则开始转移文件
                         $file_dest_dir = UPLOADFILE_DIR_HOMEWORK . DS . date('Ym') . DS . date('d');
                         if (corefile::new_dir($file_dest_dir) == true) {
                             $file_dest = '';
                             $file_dest_ls = $file_dest_dir . DS . iconv('UTF-8','GBK',$_FILES[$upload_post_name]['name']);
                             if (corefile::is_file($file_dest_ls)) {
                                 $file_dest = $file_dest_dir . DS . rand(1, 9999) . iconv('UTF-8','GBK',$_FILES[$upload_post_name]['name']);
                             } else {
                                 $file_dest = $file_dest_ls;
                             }
                             if (corefile::move_upload($_FILES[$upload_post_name]["tmp_name"], $file_dest) == true) {
                                 $post_res = $oapost->add($_FILES[$upload_post_name]['name'], '', 0, $post_type, 0, $post_user, $post_file_sha1, $_FILES[$upload_post_name]['name'], $file_dest, 'public', $_FILES[$upload_post_name]['type']);
                                 if ($post_res > 0) {
                                     //上传成功，创建记录
                                     $upload_id = $post_res;
                                 } else {
                                     corefile::delete_file($file_dest);
                                     $message = '文件上传失败，无法创建相关数据。';
                                     $message_bool = false;
                                 }
                             } else {
                                 $message = '文件上传失败，无法移动文件。';
                                 $message_bool = false;
                             }
                         } else {
                             $message = '文件上传失败，无法操作目录。';
                             $message_bool = false;
                         }
                     }
                     //添加文件引用
                     if ($upload_id > 0 && $message == '') {
                         $post_res_user = $oapost->add($upload_name, '', 0,$post_type, $upload_id, $post_user, null, $upload_name, null, 'private', null);
                         //上传成功
                         $message = '上传文件成功！';
                         $message_bool = true;
                     }
                 } else {
                     $message = '文件上传失败，您不能上传这种文件';
                     if (is_array($config_uploadfile_hibit_type_arr) == true) {
                         $message .= '：' . implode('、', $config_uploadfile_hibit_type_arr);
                     }
                     $message_bool = false;
                 }
             } else {
                 $message = '文件上传失败，文件必须在' . $config_uploadfile_min . ' KB到' . $config_uploadfile_max . ' KB之间。';
                 $message_bool = false;
             }
         } else {
             $message = '系统已经关闭了文件上传功能。';
             $message_bool = false;
         }
     } else {
         $message = '文件上传失败，发生未知异常。';
         $message_bool = false;
     }
 }

/**
 * 初始化基础变量
 * @since 1
 */
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$max = 30;
$sort = 0;
$desc = true;
$post_type = 'task';
$post_status = 'public';
$post_parent = '';
if(isset($_GET['status'])){
    $post_status = null;
}

/**
 * 提示消息变量
 * @since 1
 */
$message = '';
$message_bool = false;

/**
 * 引入时间处理插件
 * @since 1
 */
require(DIR_LIB . DS . 'plug-date.php');

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
                $message = '无法修改作业。';
                $message_bool = false;
            }
        } else {
            $message = '无法修改作业，找不到该作业。';
            $message_bool = false;
        }
    } else {
        $message = '无法修改作业，您必须输入作业标题和描述。';
        $message_bool = false;
    }
}

/**
 * 改变作业状态
 * @since 1
 */
if (isset($_GET['edit_status']) == true && (isset($_GET['finish']) == true || isset($_GET['trash']) == true)) {
    $edit_status = 'public-ready';
    if(isset($_GET['finish']) == false){
        $edit_status = 'public-trash';
    }
    $edit_view = $oapost->view($_GET['edit_status']);
    if ($edit_view) {
        if ($oapost->edit($edit_view['id'], $edit_view['post_title'], $edit_view['post_content'], $post_type, $edit_view['post_parent'], $edit_view['post_user'], $edit_view['post_password'], $edit_view['post_name'], $edit_view['post_url'], $edit_status, $edit_view['post_meta']) == true) {
            $message = '修改状态成功。';
            $message_bool = true;
        } else {
            $message = '无法修改该作业状态。';
            $message_bool = false;
        }
    }
}

/**
 * 获取作业列表记录数
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
 * 获取作业列表
 * @since 1
 */
$table_list = $oapost->view_list($post_user, null, null, $post_status, $post_type, $page, $max, $sort, $desc, $post_parent);

/**
 * 获取状态标记
 * @since 1
 * @param string $status 状态
 * @return string
 */
function get_tag_status($status) {
    $return = '';
    if ($status === 'public-finish') {
        $return = '&nbsp;&nbsp;<span class="label label-success">审批合格</span>';
    } else if ($status === 'public-ready') {
        $return = '&nbsp;&nbsp;<span class="label label-info">等待审批</span>';
    } else if ($status === 'public-trash') {
        $return = '&nbsp;&nbsp;<span class="label label-inverse">放弃</span>';
    } else if ($status === 'public-fail') {
        $return = '&nbsp;&nbsp;<span class="label label-important">没有完成</span>';
    } else {
        $return = '&nbsp;&nbsp;<span class="label">正在进行中</span>';
    }
    return $return;
}
?>
<!-- 管理表格 -->
<div class="admin-content">
    <div class="admin-content-body">

    <div class="am-cf am-padding">
      <div class="am-fl am-cf">
        <strong class="am-text-primary am-text-lg">课堂作业</strong> / <small>你作业写完了吗?</small>
      </div>
    </div>

    <hr>

    <div class="am-g">
      <div class="am-u-sm-12">
        <a href="<?php echo $page_url;?>" class="am-btn am-btn-primary am-round am-<?php if($post_status=='public'){ echo 'disabled'; } ?>"><i class="am-icon-gear am-icon-spin"></i> 查看当前计划</a>&nbsp
        <a href="<?php echo $page_url; ?>&status=all" class="am-btn am-btn-success am-round am-<?php if($post_status===null){ echo 'disabled'; } ?>"><i class="am-icon-refresh am-icon-spin"></i> 查看所有作业</a>
    <hr>
    <form class="am-form">
      <table class="am-table am-table-bordered am-table-hover color-table-bordered">
      <thead>
      <tr>
        <th class="table-author"><span class="am-icon-clipboard"></span> 作业</th>
        <?php if($post_status !== null){ ?>
        <th class="table-date"><span class="am-icon-calendar"></span> 期限</th>
        <?php }else{ ?>
        <th class="am-hide-md-down"><span class="am-icon-paw"></span> 状态</th>
        <?php } ?>
        <th class="table-set"><span class="am-icon-gear"></span> 操作</th>
      </tr>
      </thead>
      <tbody id="message_list">
        <?php if ($table_list) {
            foreach ($table_list as $v) {
                $v_parent_view = $oapost->view($v['post_parent']);
                if($v_parent_view){
                ?>
                <tr>
                    <td><a href="<?php echo $page_url.'&view='.$v['id'].'#view'; ?>" target="_self"><?php echo $v['post_title']; ?></a></td>
                    <?php if($post_status !== null){ ?>
                    <td><?php echo plugdate_get($v_parent_view['post_name']).' - '.plugdate_get($v_parent_view['post_url']); ?></td>
                    <?php }else{ ?>
                    <td class="am-hide-md-down"><?php echo get_tag_status($v['post_status']); ?></td>
                    <?php } ?>
                    <td>
                        <div class="btn-group">
                              <a href="<?php echo $page_url.'&view='.$v['id']; ?>"><div class="am-btn am-btn-primary am-btn-xs am-radius"><i class="am-icon-search"></i> 详情</div></a>&nbsp
                              <?php if($post_status !== null){ ?>
                              <a href="<?php echo $page_url.'&edit_status='.$v['id']; ?>&finish=1"><div class="am-btn am-btn-secondary am-btn-xs am-radius am-hide-md-down"><i class="am-icon-heartbeat"></i> 完成</div></a>&nbsp
                              <?php if ($logged_admin == true || $logged_teacher == true) { ?>
                              <a href="<?php echo $page_url.'&edit_status='.$v['id']; ?>&trash=1"><div class="am-btn am-btn-warning am-btn-xs am-radius am-hide-md-down"><i class="am-icon-trash-o"></i> 放弃</div></a>&nbsp
                              <?php } ?>
                              <?php if($logged_admin == true || $logged_teacher == true){ ?>
                              <a href="<?php echo $page_url.'&edit='.$v['id']; ?>"><div class="am-btn am-btn-danger am-btn-xs am-radiusr am-hide-md-down"><i class="am-icon-pencil"></i> 修改</div></a>
                              <?php } ?>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
            <?php } } } ?>
      </tbody>
    </table>
    </form>
  </div>
</div>

    <div class="am-cf">
      <ul class="am-pagination am-default admin-content-pagination">
        <li class="am-<?php if ($page <= 1) { echo ' disabled'; } ?>" style="float:left; padding-left: 25px;"><a href="<?php echo $page_url . '&page=' . $page_prev; ?>">&laquo;上一页</a></li>
        <li class="am-<?php if ($page >= $page_max) { echo ' disabled'; } ?>" style="float:right; margin-right:25px;"><a href="<?php echo $page_url.'&page='.$page_next; ?>">下一页&raquo;</a></li>
      </ul>
    </div>
    <br>
    <div class="am-cf">

      <br>
      <hr>

      <?php if (isset($_GET['view']) == true) { $view_res = $oapost->view($_GET['view']); if($view_res){ $view_parent_res = $oapost->view($view_res['post_parent']); if($view_parent_res) { ?>
      <div class="am-cf am-padding">
        <div class="am-fl am-cf">
          <strong class="am-text-primary am-text-lg">查看作业</strong> / <small>是不是很简单?</small>
        </div>
      </div>

      <div class="am-u-md-8 am-u-end color-margin-bottom">
        <div class="color-card color-card-bordered color-card-color">
            <div class="color-card-head">
                <div class="color-card-head-title"> <i class="am-icon-delicious"></i> Your Homework</div>
            </div>
            <div class="color-card-body">
              <p><a id="msg"></a></p>
              <p><?php echo $view_parent_res['post_title'].'&nbsp;&nbsp;'.get_tag_status($view_res['post_status']); ?></p>
              <p>&nbsp;</p>
              <p>作业期限：<?php echo plugdate_get($view_parent_res['post_name']).' - '.plugdate_get($view_parent_res['post_url']); ?></p>
              <p>&nbsp;</p>
              <p>作业详情：<?php echo $view_parent_res['post_content']; ?></p>
              <p>&nbsp;</p>
              <p>补充：<?php echo $view_res['post_content']; ?></p>
              <p>&nbsp;</p>
            </div>

        </div>
      </div>
      <form class="form-horizontal form-actions" action="<?php echo $page_url; ?>" method="post" enctype="multipart/form-data">

        <div class="am-u-md-8 am-u-end color-margin-bottom">
            <div class="color-card color-card-bordered color-card-color color-card-radius">
                <div class="color-card-head">
                    <div class="color-card-head-title"> <i class="am-icon-th-large"></i> 选择文件</div>
                </div>
                <div class="color-card-body" style="margin-left:25px;">
                  <div class="control-group">
                      <div class="controls">
                          <!-- <input type="file" class="btn color-btn" id="add_uploadfile" name="add_uploadfile" data-am-popover="{theme:'warning', content: '选择你的文件',trigger: 'hover focus'}"/> -->
                          <div class="am-form-group am-form-file">
                            <button type="button" class="am-btn am-btn-success am-btn-sm">
                              <i class="am-icon-cloud-upload"></i> 添加附件</button>
                            <input id="doc-form-file" type="file" name="add_uploadhomework" multiple>
                          </div>
                          <div id="file-list"></div>
                      </div>
                  </div>
                  <br>
                  <div class="control-group">
                      <div class="controls">
                          <a id="msg"></a>
                          <button type="submit" class="am-btn color-btn" data-am-popover="{theme:'warning', content: '上传你的文件', trigger: 'hover focus'}">
                              提交作业
                          </button>
                          <hr>
                          <div class="control-group">
                              <div class="controls">
                                  <?php if ($logged_admin == true || $logged_teacher == true) {?>
                                  <a href="<?php echo $page_url.'&edit='.$view_res['id']; ?>#edit" role="button" class="btn"><div class="am-btn am-btn-success am-btn-xs am-radius"><i class="am-icon-pencil"></i> 编辑</div></a>&nbsp
                                  <?php } ?>
                                  <a href="<?php echo $page_url; ?>" role="button" class="btn"><div class="am-btn am-btn-danger am-btn-xs am-radius"><i class="am-icon-reply"></i> 返回</div></a>
                              </div>
                          </div>
                      </div>
                  </div>
                </div>
            </div>
        </div>
      </form>
      <?php } } } ?>

      <?php if (isset($_GET['edit']) == true && isset($_GET['view']) == false) {  $view_res = $oapost->view($_GET['edit']); if($view_res){ ?>
        <div class="am-cf am-padding">
          <div class="am-fl am-cf">
              <strong class="am-text-primary am-text-lg">编辑成为你的计划</strong> / <small>Do everything you really like!</small>
          </div>
        </div>

        <br>
        <hr>

        <div>
            <div>
              <form class="am-form am-form-horizontal form-actions" action="<?php echo $page_url.'&view='.$view_res['id']; ?>" method="post">
                <div class="am-form-group am-g-fixed" class="am-u-sm-3 am-form-label">
                    <lable for="new_task" class="am-u-sm-3"></lable>
                    <div class="am-form-group am-g-fixed">
                        <label for="edit_title" class="am-u-sm-3 am-form-label">新的计划名称 / Plan name</label>
                        <div class="am-u-sm-9 am-form-group">
                          <textarea id="edit_title" name="edit_title" placeholder="请输入你的计划名称..." value="<?php echo $view_res['post_title']; ?>"></textarea>
                        </div>
                        <hr>
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
                            <button type="submit" class="am-btn am-radius am-btn-sm am-fl color-btn" type="submit" data-am-popover="{theme: 'warning', content: 'Believe yourself.', trigger: 'hover focus'}"><i class="am-icon-pencil"></i> 修改</button>&nbsp
                            <a href="<?php echo $page_url.'&view='.$view_res['id']; ?>" role="button" class="am-btn color-btn" data-am-popover="{content: '返回上一级', trigger: 'hover focus'}"><i class="am-icon-reply"></i> 返回</a>
                        </div>
                    </div>
              </div>
            </form>
      <?php } } ?>
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


    $(function() {
     $('#doc-form-file').on('change', function() {
       var fileNames = '';
       $.each(this.files, function() {
         fileNames += '<span class="am-badge">' + this.name + '</span> ';
       });
       $('#file-list').html(fileNames);
     });
   });
</script>
