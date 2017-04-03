<?php

/**
 * 共享默云
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
$upload_post_name = 'add_uploadfile';
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
                        $file_dest_dir = UPLOADFILE_DIR . DS . date('Ym') . DS . date('d');
                        if (corefile::new_dir($file_dest_dir) == true) {
                            $file_dest = '';
                            //将后台上传文件名由Utf8转化为GBK格式
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
                        $post_res_user = $oapost->add($upload_name, '', 0, $post_type, $upload_id, $post_user, null, $upload_name, null, 'private', null);
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
 * 修改文件信息
 * @since 1
 */
if (isset($_POST['edit_id']) == true && isset($_POST['edit_title']) == true && isset($_POST['edit_content']) == true) {
    $edit_view = $oapost->view($_POST['edit_id']);
    if ($edit_view) {
        if ($oapost->edit($_POST['edit_id'], $_POST['edit_title'], $_POST['edit_content'], $post_type, $edit_view['post_parent'], $edit_view['post_user'], $edit_view['post_password'], $edit_view['post_name'], $edit_view['post_url'], $edit_view['post_status'], $edit_view['post_meta']) == true) {
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
 * 删除文件
 * @since 1
 */
if (isset($_GET['del']) == true) {
    $del_view = $oapost->view($_GET['del']);
    if ($del_view) {
        $del_view_list = $oapost->view_list_row(null, null, null, null, $post_type, $del_view['post_parent']);
        if ($del_view_list < 2) {
            $del_parent = $oapost->view($del_view['post_parent']);
            if ($del_parent) {
                if ($oapost->del($del_parent['id']) == true) {
                    if (!corefile::delete_file($del_parent['post_url'])) {
                        $message = '无法删除该文件。';
                        $message_bool = false;
                    }
                } else {
                    $message = '无法删除该文件。';
                    $message_bool = false;
                }
            }
        }
        //删除引用ID
        if ($oapost->del($_GET['del']) == true && $message == '') {
            $message = '删除成功。';
            $message_bool = true;
        } else {
            $message = '无法删除该文件。';
            $message_bool = false;
        }
    } else {
        $message = '无法删除该文件。';
        $message_bool = false;
    }
}

/**
 * 获取消息列表记录数
 * @since 1
 */
$table_list_row = $oapost->view_list_row($post_user, null, null, $post_status, $post_type, '');

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
$table_list = $oapost->view_list($post_user, null, null, $post_status, $post_type, $page, $max, $sort, $desc, '');
?>
<!-- 管理表格 -->
<div class="admin-content">
    <div class="admin-content-body">

    <div class="am-cf am-padding">
      <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">文件共享中心</strong> / <small>独乐乐不如众乐乐</small></div>
    </div>

    <hr>

    <div class="am-g">
      <div class="am-u-sm-12">
        <div style="margin-left:20px;">
        <a href="<?php echo $page_url; ?>&status=private" class="am-icon-btn am-secondary am-icon-lock btn btn-inverse<?php if($post_status=='private'){ echo ' disabled'; } ?>" data-am-popover="{content: '个人文件列表', trigger: 'hover focus'}"></a>&nbsp;
        <a href="<?php echo $page_url; ?>&status=public" class="am-icon-btn am-warning am-icon-shield btn btn-info<?php if($post_status=='public'){ echo ' disabled'; } ?>" data-am-popover="{content: '共享文件列表', trigger: 'hover focus'}"></a>
        </div>
        <hr>
        <form class="am-form">
          <table class="am-table am-table-bordered am-table-hover color-table-bordered">
          <thead>
          <tr>
            <th class="table-author"><span class="am-icon-user"></span> 文件名称</th><th class="table-date  am-hide-md-down"><span class="am-icon-file"></span> 时间</th><th class="table-set"><span class="am-icon-gear"></span> 操作</th>
          </tr>
          </thead>
        <tbody>
          <?php if ($table_list) {
            foreach ($table_list as $v) { ?>
                <tr>
                    <td><a href="<?php echo $page_url.'&view='.$v['id']; ?>" target="_self"><?php echo $v['post_title']; ?></a></td>
                    <td class="am-hide-md-down"><?php echo $v['post_date']; ?></td>
                    <td>
                      <div class="btn-group">
                        <a href="<?php echo $page_url.'&view='.$v['id']; ?>#view" class="btn"><div class="am-btn am-btn-default am-btn-xs am-radius am-hide-md-down"><i class="am-icon-search"></i> 查看</div></a>
                        <a href="<?php echo $page_url.'&share='.$v['id']; ?>" class="btn btn-<?php if($post_status=='public'){ echo 'inverse'; }else{ echo 'info'; } ?>"><div class="am-btn am-btn-success am-btn-xs am-radius"><i class="am-icon-<?php if($post_status=='public'){ echo 'lock'; }else{ echo 'share-alt'; } ?> icon-white"></i> <?php if($post_status=='public'){ echo '取消分享'; }else{ echo '分享'; } ?></div></a>
                        <a href="file_download.php?id=<?php echo $v['id']; ?>" class="btn"><div class="am-btn am-btn-default am-btn-xs am-radius"><i class="am-icon-download"></i> 下载</div></a>
                        <a href="<?php echo $page_url.'&edit='.$v['id']; ?>#edit" role="button" class="btn"><div class="am-btn am-btn-default am-btn-xs am-radiusr am-hide-md-down"><i class="am-icon-pencil"></i> 编辑</div></a>
                        <a href="<?php echo $page_url.'&del='.$v['id']; ?>" class="btn btn-danger"><div class="am-btn am-btn-danger am-btn-xs am-radiusr am-hide-md-down"><i class="am-icon-trash-o"></i> 删除</div></a>
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
     <ul class="am-pagination am-default admin-content-pagination">
       <li class="am-<?php if ($page <= 1) { echo ' disabled'; } ?>" style="float:left; padding-left: 25px;"><a href="<?php echo $page_url . '&page=' . $page_prev; ?>">&laquo;上一页</a></li>
       <li class="am-<?php if ($page >= $page_max) { echo ' disabled'; } ?>" style="float:right; margin-right:25px;"><a href="<?php echo $page_url.'&page='.$page_next; ?>">下一页&raquo;</a></li>
     </ul>
   </div>

    <!-- 上传文件 -->
    <div class="am-cf">
      <?php if (isset($_GET['view']) == false && isset($_GET['edit']) == false) { ?>
      <!-- 添加 -->
      <div class="am-cf am-padding">
        <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">上传文件</strong> / <small>Send file to other people.</div>
      </div>
      <hr>
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
                              <i class="am-icon-cloud-upload"></i> 选择要上传的文件</button>
                            <input id="doc-form-file" type="file" name="add_uploadfile" multiple>
                          </div>
                          <div id="file-list"></div>
                      </div>
                  </div>
                  <br>
                  <div class="control-group">
                      <div class="controls">
                          <a id="msg"></a>
                          <button type="submit" class="am-btn color-btn" data-am-popover="{theme:'warning', content: '上传你的文件', trigger: 'hover focus'}">
                              开始上传
                          </button>
                      </div>
                  </div>
                </div>
            </div>
        </div>
      </form>
      <?php } ?>
    </div>


    <div class="am-cf">
      <?php if (isset($_GET['view']) == true) { $view_res = $oapost->view($_GET['view']); if($view_res){ ?>
      <!-- 查看信息 -->
      <div class="am-cf am-padding">
        <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">查看文件</strong> / <small>Do you care about me? </small></div>
      </div>
      <hr>
      <div id="view" class="form-actions form-horizontal">

        <div class="am-u-md-8 am-u-end color-margin-bottom">
            <div class="color-card color-card-bordered color-card-color">
                <div class="color-card-head">
                    <div class="color-card-head-title"> <i class="am-icon-file-o"></i> 详细信息</div>
                </div>
                <div class="color-card-body">
                  <dl class="dl-horizontal">
                      <dt>文件名称</dt>
                      <dd><?php echo $view_res['post_title']; ?></dd>
                      <dt>上传时间</dt>
                      <dd><?php echo $view_res['post_date']; ?></dd>
                      <dt>文件说明</dt>
                      <dd><?php echo $view_res['post_content']; ?></dd>
                  </dl>
                  <dl>
                      <a id="msg"></a>
                  </dl>
                  <div class="control-group">
                      <div class="controls">
                          <a href="file_download.php?id=<?php echo $view_res['id']; ?>" class="btn"><div class="am-btn am-btn-success am-btn-xs am-radius"><i class="am-icon-download"></i> 下载</div></a>&nbsp
                          <a href="<?php echo $page_url.'&edit='.$view_res['id']; ?>#edit" role="button" class="btn"><div class="am-btn am-btn-primary am-btn-xs am-radius"><i class="am-icon-pencil"></i> 编辑</div></a>&nbsp
                          <a href="<?php echo $page_url; ?>" role="button" class="btn"><div class="am-btn am-btn-danger am-btn-xs am-radius"><i class="am-icon-reply"></i> 返回</div></a>
                      </div>
                  </div>
                </div>
            </div>
        </div>

      </div>
      <?php } } ?>
    </div>

    <div class="am-cf">
    <?php if (isset($_GET['edit']) == true && isset($_GET['view']) == false) {  $view_res = $oapost->view($_GET['edit']); if($view_res){ ?>
    <!-- 编辑 -->
    <!-- <div class="am-cf am-padding">
      <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">编辑文件信息</strong> / <small>Change it.</div>
    </div> -->
    <hr>
    <form class="form-horizontal form-actions" action="<?php echo $page_url.'&view='.$view_res['id']; ?>" method="post">

      <div class="am-u-md-8 am-u-end color-margin-bottom">
          <div class="color-card color-card-bordered color-card-color">
              <div class="color-card-head">
                  <div class="color-card-head-title" id="msg"> <i class="am-icon-file-o"></i> 编辑文件信息</div>
              </div>
              <div class="color-card-body">
                <div class="control-group">
                    <label class="control-label" for="edit_title">文件名称</label>
                    <div class="controls">
                        <input type="text" id="edit_title" name="edit_title" placeholder="文件名称" value="<?php echo $view_res['post_title']; ?>">
                    </div>
                    <div class="hidden" style="overflow: hidden; text-indent: -9999px;">
                        <input type="text" name="edit_id" value="<?php echo $view_res['id']; ?>">
                    </div>
                    <div class="hidden">
                        <a type="text" name="edit_id" value="<?php echo $view_res['id']; ?>"></a>
                    </div>
                </div>
                <hr>
                <div class="control-group">
                    <label class="control-label" for="edit_content">文件描述</label>
                    <div class="controls">
                        <input type="text" id="edit_content" name="edit_content" placeholder="文件描述" value="<?php echo $view_res['post_content']; ?>">
                    </div>
                </div>
                <hr>
                <div class="control-group">
                        <div class="controls">
                          <button type="submit" class="am-btn color-btn" data-am-popover="{theme:'warning', content: '修改文件', trigger: 'hover focus'}">
                            <i class="am-icon-pencil"></i> 修改
                          </button>

                        <a href="<?php echo $page_url.'&view='.$view_res['id']; ?>" role="button" class="am-btn color-btn" data-am-popover="{content: '返回上一级', trigger: 'hover focus'}"><i class="am-icon-reply"></i> 取消</a>
                    </div>
                </div>

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
