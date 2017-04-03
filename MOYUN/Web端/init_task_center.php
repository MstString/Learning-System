<?php
/**
 * 作业发布中心
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
 * 获取所有用户组
 */

$group_list = $moyunuser->view_group_list(1, 999, 0, true);

/**
 * 初始化基础变量
 * @since 1
 */
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$max = 30;
$sort = 0;
$desc = true;
$post_type = 'task';
$post_status = isset($_GET['status']) ? $_GET['status'] : 'public';
$post_parent = isset($_GET['parent']) ? $_GET['parent'] : '0';
if($post_parent > 0){
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
 * 添加新的记录
 * @since 1
 */
if (isset($_POST['add_title']) == true && isset($_POST['add_content']) == true && isset($_POST['add_date_start']) == true && isset($_POST['add_date_maturity']) == true) {
    if ($_POST['add_title'] && $_POST['add_content']) {
        $post_date_start = plugdate_check($_POST['add_date_start']);
        $post_date_maturity = plugdate_check($_POST['add_date_maturity']);
        if ((int) $post_date_start <= (int) $post_date_maturity) {
            if ($oapost->add($_POST['add_title'], $_POST['add_content'], $_POST['add_group'], $post_type, 0, $post_user, null, $post_date_start, $post_date_maturity, 'public', null)) {
                $message = '添加作业成功。';
                $message_bool = true;
            } else {
                $message = '无法添加新的作业。';
                $message_bool = false;
            }
        } else {
            $message = '无法添加新的作业，结束时间必须大于开始时间。';
            $message_bool = false;
        }
    } else {
        $message = '无法添加作业，请正确填写相关信息。';
        $message_bool = false;
    }
}

/**
 * 用户接受作业
 * @since 1
 */
if (isset($_GET['accept']) == true) {
    $accept_view = $oapost->view($_GET['accept']);
    if ($accept_view) {
        if ($accept_view['post_status'] === 'public' && $accept_view['post_parent'] == 0 && (int) $accept_view['post_url'] > (int) date('Ymd')) {
            $accept_parent = $oapost->view_list_row($post_user, null, null, null, $post_type, $accept_view['id']);
            if ($accept_parent < 1) {
                if ($oapost->add($accept_view['post_title'], '无', $accept_view['user_group'], $post_type, $accept_view['id'], $post_user, null, null, null, 'public', null)) {
                    $message = '添加作业成功。';
                    $message_bool = true;
                } else {
                    $message = '无法添加新的作业。';
                    $message_bool = false;
                }
            } else {
                $message = '您已经接受过该作业了。';
                $message_bool = false;
            }
        } else {
            $message = '该作业已经结束了。';
            $message_bool = false;
        }
    } else {
        $message = '该作业不存在';
        $message_bool = false;
    }
}

/**
 * 修改记录信息
 * @since 1
 */
if (isset($_POST['edit_id']) == true && isset($_POST['edit_title']) == true && isset($_POST['edit_content']) == true && isset($_POST['edit_date_start']) == true && isset($_POST['edit_date_maturity']) == true) {
    if ($_POST['edit_title'] && $_POST['edit_content']) {
        $edit_view = $oapost->view($_POST['edit_id']);
        if ($edit_view) {
            $post_date_start = plugdate_check($_POST['edit_date_start']);
            $post_date_maturity = plugdate_check($_POST['edit_date_maturity']);
            if ((int) $post_date_start <= (int) $post_date_maturity) {
                if ($oapost->edit($_POST['edit_id'], $_POST['edit_title'], $_POST['edit_content'], $post_type, $edit_view['post_parent'], $edit_view['post_user'], $edit_view['post_password'], $post_date_start, $post_date_maturity, $edit_view['post_status'], $edit_view['post_meta']) == true) {
                    $message = '修改成功！';
                    $message_bool = true;
                } else {
                    $message = '无法修改作业。';
                    $message_bool = false;
                }
            } else {
                $message = '无法修改作业，结束时间必须大于开始时间';
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
 * 完结作业
 * @since 1
 */
if (isset($_GET['edit_finish']) == true) {
    $edit_finish_boolean = false;
    $edit_finish_parent_ready_view = $oapost->view_list_row(null, null, null, 'public-ready', $post_type, $_GET['edit_finish']);
    $edit_finish_parent_view = $oapost->view_list_row(null, null, null, 'public', $post_type, $_GET['edit_finish']);
    if ($edit_finish_parent_ready_view > 0 || $edit_finish_parent_view > 0) {
        $edit_finish_boolean = false;
    } else {
        $edit_finish_boolean = true;
    }
    $edit_view = $oapost->view($_GET['edit_finish']);
    if ($edit_view) {
        if ($edit_finish_boolean) {
            if ($oapost->edit($edit_view['id'], $edit_view['post_title'], $edit_view['post_content'], $post_type, $edit_view['post_parent'], $edit_view['post_user'], $edit_view['post_password'], $edit_view['post_name'], $edit_view['post_url'], 'public-finish', $edit_view['post_meta']) == true) {
                $message = '成功完结了该作业。';
                $message_bool = true;
            } else {
                $message = '无法设定为完结作业。';
                $message_bool = false;
            }
        } else {
            $message = '无法设定为完结作业，该作业下的学生完成状况尚未完全批改。';
            $message_bool = false;
        }
    }
}

/**
 * 批改作业
 * @since 1
 */
if ($post_parent > 0 && isset($_GET['parent_view']) == true && isset($_GET['edit_status']) == true) {
    $edit_view = $oapost->view($_GET['parent_view']);
    if ($edit_view) {
        if ($oapost->edit($_GET['parent_view'], $edit_view['post_title'], $edit_view['post_content'], $post_type, $edit_view['post_parent'], $edit_view['post_user'], $edit_view['post_password'], $edit_view['post_name'], $edit_view['post_url'], $_GET['edit_status'], $edit_view['post_meta']) == true) {
            $message = '批改成功';
            $message_bool = true;
        } else {
            $message = '无法批改该作业。';
            $message_bool = false;
        }
    }
}

/**
 * 设定业绩
 * @since 1
 */
if ($post_parent > 0 && isset($_GET['parent_view']) == true && isset($_POST['set_results']) == true) {
    $results_task_view = $oapost->view($_GET['parent_view']);
    if ($results_task_view) {
        $results_view = $oapost->view_list(null, null, null, null, 'performance', 1, 10, 0, false, $_GET['parent_view']);
        if ($results_view) {
            if ($oapost->edit($results_view[0]['id'], $results_view[0]['post_title'], '', 'performance', $results_view[0]['post_parent'], $results_view[0]['post_user'], $results_view[0]['post_password'], $results_view[0]['post_name'], $_POST['set_results'], $results_view[0]['post_status'], $results_view[0]['post_meta']) == true) {
                $message = '设定业绩量成功。';
                $message_bool = true;
            } else {
                $message = '无法设定业绩量。';
                $message_bool = false;
            }
        } else {
            if ($oapost->add($results_task_view['post_title'], '无', 0, 'performance', $results_task_view['id'], $results_task_view['post_user'], null, $post_user, $_POST['set_results'], 'private', null)) {
                $message = '设定业绩量成功。';
                $message_bool = true;
            } else {
                $message = '无法设定业绩量。';
                $message_bool = false;
            }
        }
    }
}

/**
 * 删除记录
 * @since 1
 */
if (isset($_GET['del']) == true) {
    $del_view = $oapost->view($_GET['del']);
    if ($del_view) {
        if ($del_view['post_status'] == 'public-trash') {
            //删除ID
            if ($oapost->del($del_view['id']) == true) {
                $message = '删除成功。';
                $message_bool = true;
            } else {
                $message = '无法彻底删除该作业。';
                $message_bool = false;
            }
        } else {
            if ($oapost->edit($del_view['id'], $del_view['post_title'], $del_view['post_content'], $post_type, $del_view['post_parent'], $del_view['post_user'], $del_view['post_password'], $del_view['post_name'], $del_view['post_url'], 'public-trash', $del_view['post_meta']) == true) {
                $message = '删除成功。';
                $message_bool = true;
            } else {
                $message = '无法删除该作业。';
                $message_bool = false;
            }
        }
    } else {
        $message = '无法删除该作业。';
        $message_bool = false;
    }
}

/**
 * 获取列表记录数
 * @since 1
 */
$table_list_row = $oapost->view_list_row(null, null, null, $post_status, $post_type, $post_parent);

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
$table_list = $oapost->view_list(null, null, null, $post_status, $post_type, $page, $max, $sort, $desc, $post_parent);

/**
 * 获取状态标记
 * @since 1
 * @param string $status 状态
 * @return string
 */
function get_tag_status($status) {
    $return = '';
    if ($status === 'public-finish') {
        $return = '&nbsp;&nbsp;<span class="label label-success">批改合格</span>';
    } else if ($status === 'public-ready') {
        $return = '&nbsp;&nbsp;<span class="label label-info">等待批改</span>';
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
          <strong class="am-text-primary am-text-lg">学习作业</strong> / <small>Learning Center</small>
        </div>
      </div>

      <hr>
      <div class="am-g am-hide-md-down">
        <div class="am-u-sm-12">

          <a class="am-btn am-btn-warning am-<?php if($post_status=='public'){ echo ' disabled'; } ?>" href="<?php echo $page_url; ?>&status=public">
            <i class="am-icon-list"></i>
            查看作业列表
          </a>
          <a class="am-btn am-btn-success am-<?php if($post_status=='public-finish'){ echo ' disabled'; } ?>" href="<?php echo $page_url; ?>&status=public-finish">
            <i class="am-icon-list-alt"></i>
            查看已完结的作业
          </a>
          <a class="am-btn am-btn-primary am-<?php if($post_status=='public-trash'){ echo ' disabled'; } ?>" href="<?php echo $page_url; ?>&status=public-trash">
            <i class="am-icon-list-ol"></i>
            查看删除的作业
          </a>

        </div>
      </div>

      <hr>

      <div class="am-g">
        <div class="am-u-sm-12">
          <form class="am-form">
            <table class="am-table am-table-bordered am-table-hover color-table-bordered">
            <thead>
            <tr>
              <th class="table-author"><span class="am-icon-file-o"></span> 作业</th>
              <?php if($post_parent == 0){ ?>
              <th class="am-hide-md-down"><span class="am-icon-calendar"></span> 创建日期</th>
              <th class="am-hide-md-down"><span class="am-icon-calendar"></span> 截至日期</th>
              <?php }else{ ?>
              <th class="table-author"><span class="am-icon-user"></span> 学生</th>
              <th class="am-hide-md-down"><span class="am-icon-pawn"></span> 状态</th>
              <?php } ?>
              <th class="table-set"><span class="am-icon-gear"></span> 操作</th>
            </tr>
            </thead>
            <tbody id="message_list">
              <?php if ($table_list) {
                foreach ($table_list as $v) { ?>
                    <tr class="<?php
                        if((int)$v['post_url'] < (int)date('Ymd') && (int)$v['post_url'] > 0 && $post_status=='public' && $post_parent == 0){
                            echo 'warning';
                        }
                        if($post_parent > 0){
                            if($v['post_status'] === 'public-ready'){
                                echo 'warning';
                            }elseif($v['post_status'] === 'public-fail'){
                                echo 'error';
                            }elseif($v['post_status'] === 'public-finish'){
                                echo 'success';
                            }
                        }
                        ?>">
                        <?php if ($logged_teacher == true){?>
                            <tr>
                            <td><a href="<?php if($post_parent == 0){ echo $page_url.'&view='.$v['id'].'#view'; }else{ echo $page_url.'&parent='.$post_parent.'&parent_view='.$v['id'].'#parent'; } ?>" target="_self"><?php echo $v['post_title']; ?></a></td>
                            <?php if($post_parent == 0){ ?>
                            <td class="am-hide-md-down "><?php echo plugdate_get($v['post_name']);?></td>
                            <td class="am-hide-md-down "><?php echo plugdate_get($v['post_url']); ?></td>
                            <?php }else{ ?>
                            <td><?php $v_user = $moyunuser->view_user($v['post_user']); if($v_user){ echo $v_user['user_name']; unset($v_user); } ?></td>
                            <td class="am-hide-md-down"><?php echo get_tag_status($v['post_status']); ?></td>
                            <?php } ?>
                            <td>
                              <a href="<?php if($post_parent == 0){ echo $page_url.'&view='.$v['id'].'#view'; }else{ echo $page_url.'&parent='.$post_parent.'&parent_view='.$v['id'].'#parent'; } ?>"><div class="am-btn am-btn-secondary am-btn-xs am-radius"><i class="am-icon-search"></i> 详情</div></a>
                              <?php if($post_status==='public' && $post_parent == 0){ ?>
                              <?php if($logged_teacher == false) { ?>
                              <a href="<?php echo $page_url.'&accept='.$v['id']; ?>"><div class="am-hide-md-down am-btn am-btn-default am-btn-xs am-radius"><i class="am-icon-pencil"></i> 接受作业</div></a>
                              <?php } ?>
                              <?php } ?>
                              <?php if($logged_teacher == true){ ?><?php if($post_status==='public' && $post_parent == 0){ ?>
                              <a href="<?php echo $page_url.'&edit_finish='.$v['id']; ?>"><div class="am-hide-md-down am-btn am-btn-danger am-btn-xs am-radius"><i class="am-icon-toggle-on"></i> 结束作业</div></a>
                              <a href="<?php echo $page_url.'&parent='.$v['id']; ?>"><div class="am-hide-md-down am-btn am-btn-warning am-btn-xs am-radius"><i class="am-icon-check"></i> 批改作业</div></a>
                              <?php } if($post_parent == 0){ ?>
                              <a href="<?php echo $page_url.'&edit='.$v['id']; ?>#edit"><div class="am-hide-md-down am-btn am-btn-primary am-btn-xs am-radius"><i class="am-icon-pencil"></i> 编辑</div></a>
                              <a href="<?php echo $page_url.'&del='.$v['id']; ?>"><div class="am-btn am-btn-danger am-btn-xs am-radius"><i class="am-icon-trash"></i> 删除</div></a>
                                <?php } } ?>
                            </td>
                            </tr>
                        <?php } else if ($logged_2014 == true && $oapost->view_power($v['id']) == 4){?>
                            <tr>
                            <td><a href="<?php if($post_parent == 0){ echo $page_url.'&view='.$v['id'].'#view'; }else{ echo $page_url.'&parent='.$post_parent.'&parent_view='.$v['id'].'#parent'; } ?>" target="_self"><?php echo $v['post_title']; ?></a></td>
                            <?php if($post_parent == 0){ ?>
                            <td class="am-hide-md-down "><?php echo plugdate_get($v['post_name']);?></td>
                            <td class="am-hide-md-down "><?php echo plugdate_get($v['post_url']); ?></td>
                            <?php }else{ ?>
                            <td><?php $v_user = $moyunuser->view_user($v['post_user']); if($v_user){ echo $v_user['user_name']; unset($v_user); } ?></td>
                            <td class="am-hide-md-down"><?php echo get_tag_status($v['post_status']); ?></td>
                            <?php } ?>
                            <td>
                              <a href="<?php if($post_parent == 0){ echo $page_url.'&view='.$v['id'].'#view'; }else{ echo $page_url.'&parent='.$post_parent.'&parent_view='.$v['id'].'#parent'; } ?>"><div class="am-btn am-btn-secondary am-btn-xs am-radius"><i class="am-icon-search"></i> 详情</div></a>
                              <?php if($post_status==='public' && $post_parent == 0){ ?>
                              <?php if($logged_teacher == false) { ?>
                              <a href="<?php echo $page_url.'&accept='.$v['id']; ?>"><div class="am-hide-md-down am-btn am-btn-default am-btn-xs am-radius"><i class="am-icon-pencil"></i> 接受作业</div></a>
                              <?php } ?>
                              <?php } ?>
                              <?php if($logged_admin || $logged_teacher){ ?><?php if($post_status==='public' && $post_parent == 0){ ?>
                              <a href="<?php echo $page_url.'&edit_finish='.$v['id']; ?>"><div class="am-hide-md-down am-btn am-btn-danger am-btn-xs am-radius"><i class="am-icon-toggle-on"></i> 结束作业</div></a>
                              <a href="<?php echo $page_url.'&parent='.$v['id']; ?>"><div class="am-hide-md-down am-btn am-btn-warning am-btn-xs am-radius"><i class="am-icon-check"></i> 批改作业</div></a>
                              <?php } if($post_parent == 0){ ?>
                              <a href="<?php echo $page_url.'&edit='.$v['id']; ?>#edit"><div class="am-hide-md-down am-btn am-btn-primary am-btn-xs am-radius"><i class="am-icon-pencil"></i> 编辑</div></a>
                              <a href="<?php echo $page_url.'&del='.$v['id']; ?>"><div class="am-btn am-btn-danger am-btn-xs am-radius"><i class="am-icon-trash"></i> 删除</div></a>
                                <?php } } ?>
                            </td>
                            </tr>
                        <?php } else if ($logged_2015 == true && $oapost->view_power($v['id']) == 5){?>
                            <tr>
                                <td><a href="<?php if($post_parent == 0){ echo $page_url.'&view='.$v['id'].'#view'; }else{ echo $page_url.'&parent='.$post_parent.'&parent_view='.$v['id'].'#parent'; } ?>" target="_self"><?php echo $v['post_title']; ?></a></td>
                                <?php if($post_parent == 0){ ?>
                                <td class="am-hide-md-down "><?php echo plugdate_get($v['post_name']);?></td>
                                <td class="am-hide-md-down "><?php echo plugdate_get($v['post_url']); ?></td>
                                <?php }else{ ?>
                                <td><?php $v_user = $moyunuser->view_user($v['post_user']); if($v_user){ echo $v_user['user_name']; unset($v_user); } ?></td>
                                <td class="am-hide-md-down"><?php echo get_tag_status($v['post_status']); ?></td>
                                <?php } ?>
                                <td>
                                  <a href="<?php if($post_parent == 0){ echo $page_url.'&view='.$v['id'].'#view'; }else{ echo $page_url.'&parent='.$post_parent.'&parent_view='.$v['id'].'#parent'; } ?>"><div class="am-btn am-btn-secondary am-btn-xs am-radius"><i class="am-icon-search"></i> 详情</div></a>
                                  <?php if($post_status==='public' && $post_parent == 0){ ?>
                                  <?php if($logged_teacher == false) { ?>
                                  <a href="<?php echo $page_url.'&accept='.$v['id']; ?>"><div class="am-hide-md-down am-btn am-btn-default am-btn-xs am-radius"><i class="am-icon-pencil"></i> 接受作业</div></a>
                                  <?php } ?>
                                  <?php } ?>
                                  <?php if($logged_admin || $logged_teacher){ ?><?php if($post_status==='public' && $post_parent == 0){ ?>
                                  <a href="<?php echo $page_url.'&edit_finish='.$v['id']; ?>"><div class="am-hide-md-down am-btn am-btn-danger am-btn-xs am-radius"><i class="am-icon-toggle-on"></i> 结束作业</div></a>
                                  <a href="<?php echo $page_url.'&parent='.$v['id']; ?>"><div class="am-hide-md-down am-btn am-btn-warning am-btn-xs am-radius"><i class="am-icon-check"></i> 批改作业</div></a>
                                  <?php } if($post_parent == 0){ ?>
                                  <a href="<?php echo $page_url.'&edit='.$v['id']; ?>#edit"><div class="am-hide-md-down am-btn am-btn-primary am-btn-xs am-radius"><i class="am-icon-pencil"></i> 编辑</div></a>
                                  <a href="<?php echo $page_url.'&del='.$v['id']; ?>"><div class="am-btn am-btn-danger am-btn-xs am-radius"><i class="am-icon-trash"></i> 删除</div></a>
                                    <?php } } ?>
                                </td>
                            </tr>
                        <?php } else if ($logged_2016 == true && $oapost->view_power($v['id']) == 6){?>
                            <tr>
                                <td><a href="<?php if($post_parent == 0){ echo $page_url.'&view='.$v['id'].'#view'; }else{ echo $page_url.'&parent='.$post_parent.'&parent_view='.$v['id'].'#parent'; } ?>" target="_self"><?php echo $v['post_title']; ?></a></td>
                                <?php if($post_parent == 0){ ?>
                                <td class="am-hide-md-down "><?php echo plugdate_get($v['post_name']);?></td>
                                <td class="am-hide-md-down "><?php echo plugdate_get($v['post_url']); ?></td>
                                <?php }else{ ?>
                                <td><?php $v_user = $moyunuser->view_user($v['post_user']); if($v_user){ echo $v_user['user_name']; unset($v_user); } ?></td>
                                <td class="am-hide-md-down"><?php echo get_tag_status($v['post_status']); ?></td>
                                <?php } ?>
                                <td>
                                  <a href="<?php if($post_parent == 0){ echo $page_url.'&view='.$v['id'].'#view'; }else{ echo $page_url.'&parent='.$post_parent.'&parent_view='.$v['id'].'#parent'; } ?>"><div class="am-btn am-btn-secondary am-btn-xs am-radius"><i class="am-icon-search"></i> 详情</div></a>
                                  <?php if($post_status==='public' && $post_parent == 0){ ?>
                                  <?php if($logged_teacher == false) { ?>
                                  <a href="<?php echo $page_url.'&accept='.$v['id']; ?>"><div class="am-hide-md-down am-btn am-btn-default am-btn-xs am-radius"><i class="am-icon-pencil"></i> 接受作业</div></a>
                                  <?php } ?>
                                  <?php } ?>
                                  <?php if($logged_admin || $logged_teacher){ ?><?php if($post_status==='public' && $post_parent == 0){ ?>
                                  <a href="<?php echo $page_url.'&edit_finish='.$v['id']; ?>"><div class="am-hide-md-down am-btn am-btn-danger am-btn-xs am-radius"><i class="am-icon-toggle-on"></i> 结束作业</div></a>
                                  <a href="<?php echo $page_url.'&parent='.$v['id']; ?>"><div class="am-hide-md-down am-btn am-btn-warning am-btn-xs am-radius"><i class="am-icon-check"></i> 批改作业</div></a>
                                  <?php } if($post_parent == 0){ ?>
                                  <a href="<?php echo $page_url.'&edit='.$v['id']; ?>#edit"><div class="am-hide-md-down am-btn am-btn-primary am-btn-xs am-radius"><i class="am-icon-pencil"></i> 编辑</div></a>
                                  <a href="<?php echo $page_url.'&del='.$v['id']; ?>"><div class="am-btn am-btn-danger am-btn-xs am-radius"><i class="am-icon-trash"></i> 删除</div></a>
                                    <?php } } ?>
                                </td>
                            </tr>
                        <?php }?>
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
      <?php if (isset($_GET['view']) == false && isset($_GET['edit']) == false && $post_parent == 0) { ?>
      <!-- 添加作业 -->
      <?php if ($logged_teacher == true) {?>
      <div class="am-cf am-padding">
        <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">布置一个作业</strong> / <small>兴趣是最好的老师！</div>
      </div>
      <form class="am-form am-form-horizontal form-actions" action="<?php echo $page_url; ?>" method="post">
        <div class="am-form-group am-g-fixed">
            <label for="add_title" class="am-u-sm-3 am-form-label">科目 / HomeWork</label>
            <div class="am-u-sm-9 am-form-group am-button-group">
              <input type="text" id="add_title" name="add_title" placeholder="作业所属科目">
              <small>老师您辛苦了...</small>
            </div>
        </div>
        <div class="am-form-group am-g-fixed">
            <label for="add_content" class="am-u-sm-3 am-form-label">作业详情 / HomeWork Content</label>
            <div class="am-u-sm-9 am-form-group am-button-group">
              <textarea rows="5" type="text" id="add_content" name="add_content" placeholder="作业的具体内容"></textarea>
              <small>老师手下留情啊 (lll￢ω￢)</small>
            </div>
        </div>
        <div class="am-form-group am-g-fixed">
            <label for="add_group" class="am-u-sm-3 am-form-label">权限选择 / Power Choose</label>
            <div class="am-u-sm-9 am-form-group am-button-group">
            <div class="input-prepend">
                <i class="icon-th"></i></span>
                <select class="input-small" id="add_group" name="add_group">
                    <?php if($group_list){ foreach($group_list as $v){
                        //限定权限的显示范围
                        if ($v['id'] >= 4) {
                    ?>
                    <option value="<?php echo $v['id']; ?>">
                    <?php
                        }
                    echo $v['group_name']; ?></option><?php } }
                    ?>
                </select>
            </div>
            </div>
        </div>
        <div class="am-form-group am-g-fixed">
          <label for="add_date_start" class="am-u-sm-3 am-form-label">作业开始时间 / Start time</label>
          <div class="am-u-sm-9 am-form-group color-am-time">
              <input type="text" id="add_date_start" name="add_date_start" class="am-form-field time-input" placeholder="选择作业的开始日期 YYYY-MM-DD" data-am-datepicker="" required="" value="">
              <a href="#date_now_button" role="button" class="btn"><i class="icon-time"></i> 选择今天</a>
          </div>
        </div>
        <div class="am-form-group am-g-fixed">
          <label for="add_date_maturity" class="am-u-sm-3 am-form-label">作业结束时间 / Stop time</label>
          <div class="am-u-sm-9 am-form-group color-am-time">
              <input type="text" id="add_date_maturity" name="add_date_maturity" class="am-form-field time-input" placeholder="选择作业的结束日期 YYYY-MM-DD" data-am-datepicker="" required="" value="">
          </div>
        </div>
        <div class="am-form-group am-g-fixed">
          <label for="add_date_maturity" class="am-u-sm-3 am-form-label"></label>
          <div class="am-u-sm-9 am-form-group am-button-group">
              <a id="msg"></a>
              <button class="am-btn color-btn" data-am-popover="{theme:'warning', content: '添加一个作业!', trigger: 'hover focus'}">
                  添加
              </button>
              <a href="<?php echo $page_url; ?>" role="button" class="am-btn color-btn" data-am-popover="{theme:'warning', content: '返回上一级', trigger: 'hover focus'}"> 返回</a>
          </div>
        </div>
      </form>
      <?php } ?>
      <?php } ?>
      <?php if (isset($_GET['view']) == true && $post_parent==0) { $view_res = $oapost->view($_GET['view']); if($view_res){ ?>
      <!-- 查看作业 -->
      <div class="am-cf am-padding">
        <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">查看作业</strong> / <small>See homework detiles...</div>
      </div>
      <div class="am-u-md-8 am-u-end color-margin-bottom">
      <div class="color-card color-card-bordered">
          <div class="color-card-head">
              <div class="color-card-head-title">作业详情</div>
          </div>
          <div class="color-card-body">
            <p><?php echo $view_res['post_title']; ?><?php if((int)$view_res['post_url'] < (int)date('Ymd') && (int)$view_res['post_url'] > 0){ echo ' - 已过期'; }?></p>
            <p>&nbsp;</p>
            <p>开始日期：<?php echo plugdate_get($view_res['post_name']); ?></p>
            <p>&nbsp;</p>
            <p>结束日期：<?php echo plugdate_get($view_res['post_url']); ?></p>
            <p>&nbsp;</p>
            <p><?php echo $view_res['post_content']; ?></p>
            <p>&nbsp;</p>
            <div class="control-group">
                <div class="controls">
                    <?php if($logged_teacher == true) { ?>
                    <a href="<?php echo $page_url.'&edit='.$view_res['id']; ?>#edit" role="button" class="btn"><div class="am-btn am-btn-success am-btn-xs am-radius"><i class="am-icon-pencil"></i> 编辑</div></a>&nbsp
                    <?php } ?>
                    <a href="<?php echo $page_url; ?>"role="button" class="btn"><div class="am-btn am-btn-danger am-btn-xs am-radius"><i class="am-icon-reply"></i> 返回</div></a>
                </div>
            </div>
          </div>
      </div>
    </div>
    <?php } } ?>

    <?php if (isset($_GET['edit']) == true && isset($_GET['view']) == false && $post_parent==0) {  $view_res = $oapost->view($_GET['edit']); if($view_res){ ?>
    <!-- 编辑作业 -->
    <div class="am-cf am-padding">
      <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">编辑作业</strong> / <small>Change homework rules...</div>
    </div>
    <form class="am-form am-form-horizontal form-actions" action="<?php echo $page_url.'&view='.$view_res['id']; ?>" method="post">
      <div class="am-form-group am-g-fixed">
          <label for="edit_title" class="am-u-sm-3 am-form-label">修改科目 / HomeWork</label>
          <div class="am-u-sm-9 am-form-group am-button-group">
            <input type="text" id="edit_title" name="edit_title" placeholder="修改作业科目" value="<?php echo $view_res['post_title']; ?>">
          </div>
          <div class="hidden" style="overflow: hidden; text-indent: -9999px;">>
              <input type="text" name="edit_id" value="<?php echo $view_res['id']; ?>">
          </div>
      </div>
      <div class="am-form-group am-g-fixed">
          <label for="edit_content" class="am-u-sm-3 am-form-label">修改作业详情 / Change Content</label>
          <div class="am-u-sm-9 am-form-group am-button-group">
            <textarea rows="5" type="text" id="edit_content" name="edit_content" placeholder="修改作业的具体内容"><?php echo $view_res['post_content']; ?></textarea>
          </div>
      </div>
      <div class="am-form-group am-g-fixed">
        <label for="edit_date_start" class="am-u-sm-3 am-form-label">修改作业开始时间 / Start time</label>
        <div class="am-u-sm-9 am-form-group color-am-time">
            <input type="text" id="edit_date_start" name="edit_date_start" class="am-form-field time-input" placeholder="选择作业的开始日期" data-am-datepicker="" readonly="readonly" required="" value="<?php echo plugdate_get($view_res['post_name']); ?>">
            <a href="#date_now_button" role="button" class="btn"><i class="icon-time"></i> 选择今天</a>
        </div>
      </div>
      <div class="am-form-group am-g-fixed">
        <label for="edit_date_maturity" class="am-u-sm-3 am-form-label">修改作业结束时间 / Stop time</label>
        <div class="am-u-sm-9 am-form-group color-am-time">
            <input type="text" id="edit_date_maturity" name="edit_date_maturity" class="am-form-field time-input" placeholder="选择作业的结束日期" data-am-datepicker="" readonly="readonly" required="" value="<?php echo plugdate_get($view_res['post_url']); ?>">
        </div>
      </div>
      <div class="am-form-group am-g-fixed">
        <label for="add_date_maturity" class="am-u-sm-3 am-form-label"></label>
        <div class="am-u-sm-9 am-form-group am-button-group">
            <a id="msg"></a>
            <button class="am-btn color-btn" data-am-popover="{theme:'warning', content: 'Amaze UI Color Style Popover', trigger: 'hover focus'}">
                修改
            </button>
            <a href="<?php echo $page_url.'&view='.$view_res['id']; ?>" role="button" class="am-btn color-btn" data-am-popover="{theme:'warning', content: '返回上一级', trigger: 'hover focus'}"> 返回</a>
        </div>
      </div>
    </form>
    <?php } }?>

    <?php
    if ($post_parent != 0 && isset($_GET['parent_view']) == true) {
        $view_res = $oapost->view($_GET['parent_view']);
        if ($view_res) {
            $view_results_view = $oapost->view_list(null, null, null, 'private', 'performance', 1, 5, 0, false, $view_res['id']);
            ?>
      <div class="am-cf am-padding">
        <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">批改作业</strong> / <small>Check HomeWork...</div>
      </div>
      <div class="am-u-md-8 am-u-end color-margin-bottom">
      <div class="color-card color-card-bordered">
          <div class="color-card-head">
              <div class="color-card-head-title">批改作业</div>
          </div>
          <div class="color-card-body">
            <p>
                        <?php
                        echo $view_res['post_title'];
                        if ((int) $view_res['post_url'] < (int) date('Ymd') && (int) $view_res['post_url'] > 0) {
                            echo ' - 已过期';
                        }
                        if ($view_res['post_status'] === 'public-finish') {
                            echo '&nbsp;&nbsp;<span class="am-badge am-badge-success am-round">合格</span>';
                        } else if ($view_res['post_status'] === 'public-ready') {
                            echo '&nbsp;&nbsp;<span class="am-badge am-badge-primary am-round">等待批改</span>';
                        } else if ($view_res['post_status'] === 'public-trash') {
                            echo '&nbsp;&nbsp;<span class="am-badge am-badge-success am-round">放弃</span>';
                        } else if ($view_res['post_status'] === 'public-fail') {
                            echo '&nbsp;&nbsp;<a class="am-badge am-badge-warning am-round">没有完成</a>';
                        } else {
                            echo '&nbsp;&nbsp;<span class="am-badge am-badge-secondary am-round">正在进行中</span>';
                        }
                        ?>
            </p>
            <p>学生姓名：
                <?php
                $view_user = $moyunuser->view_user($view_res['post_user']);
                if($view_user){
                    echo $view_user['user_name'];
                }
                ?>
            </p>
            <p>作业说明：<?php echo $view_res['post_content']; ?></p>
            <form class="am-form am-form-horizontal form-actions" action="<?php echo $page_url.'&parent='.$post_parent.'&parent_view='.$view_res['id']; ?>" method="post">
              <input style="width:20%;" type="text" id="set_results" name="set_results" placeholder="0" value="<?php if(isset($view_results_view[0]['post_url'])){ echo $view_results_view[0]['post_url']; } ?>">
              <hr>
              <button type="submit" class="am-btn color-btn" data-am-popover="{theme:'warning', content: '打分', trigger: 'hover focus'}">
                <i class="am-icon-pencil"></i> 确定
              </button>
              <hr>
                <?php if ($logged_teacher == true) {?>
                <a href="http://localhost/MOYUN/content/homework/" target="_blank"><div class="am-btn color-btn"><i class="am-icon-download"></i> 下载附件</div></a>&nbsp;
                <?php }?>
                <a href="<?php echo $page_url.'&parent='.$post_parent.'&parent_view='.$view_res['id']; ?>&edit_status=public-finish#parent" role="button" class="am-btn color-btn" data-am-popover="{theme:'warning', content: '^_^', trigger: 'hover focus'}"><i class="am-icon-check"></i> 及格</a>
                <a href="<?php echo $page_url.'&parent='.$post_parent.'&parent_view='.$view_res['id']; ?>&edit_status=public-fail#parent" role="button" class="am-btn color-btn" data-am-popover="{theme:'warning', content: 'X_X', trigger: 'hover focus'}"><i class="am-icon-close"></i> 不及格</a>
                <a href="init.php?init=1&user=<?php echo $view_res['post_user']; ?>#send" role="button" class="am-btn color-btn" data-am-popover="{theme:'warning', content: '^_~', trigger: 'hover focus'}"><i class="am-icon-envelope"></i> 私信</a>
                <a href="<?php echo $page_url; ?>" role="button" class="am-btn color-btn" data-am-popover="{theme:'warning', content: '返回上一级', trigger: 'hover focus'}"><i class="am-icon-reply"></i> 返回</a>
            </form>
            <br>

          </div>
      </div>

      </div>
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

       //时间选择今天按钮
       $("a[href='#date_now_button']").click(function(){
           $(this).prev().attr("value","<?php echo date('Y-m-d'); ?>");
       });
    });
</script>
