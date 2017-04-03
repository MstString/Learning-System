<?php
/**
 * 个人首页
 * @author string.zhengyang@gmail.com
 * @version 2
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
 * 获取消息列表
 * @since 1
 */
$message_list = $oapost->view_list(null, null, null, 'private', 'message', 1, 6, 0, true, null, $post_user);

/**
 * 获取系统消息
 * @since 3
 */
$system_message_list = $oapost->view_list(null, null, null, 'public', 'message', 1, 1, 0, true, null, null);
$system_message_view = null;
if ($system_message_list) {
    $system_message_view = $oapost->view($system_message_list[0]['id']);
}
unset($system_message_list);

/**
 * 计算任务信息
 * @since 1
 */
$task_user_count = $oapost->view_list_row($post_user, null, null, 'public-finish', 'task', '');
if(!$task_user_count){
    $task_user_count = 0;
}
$task_count = $oapost->view_list_row(null, null, null, 'public', 'task', 0);
if(!$task_count){
    $task_count = 0;
}

/**
 * 计算作业成绩
 * @since 1
 */
$performance_count = $oapost->sum_fields('performance', $post_user, 'post_url');
$date_mouth_start = date('Y-m') . '-00 00:00:00';
$date_mouth_end = date('Y') . '-' . ((int) date('m') + 1) . '-00 00:00:00';
$performance_mouth_count = $oapost->sum_fields('performance', $post_user, 'post_url', $date_mouth_start, $date_mouth_end);
?>

<!-- 显示页面 -->
<div class="admin-content">
   <div class="admin-content-body am-g-fixed">

    <div class="am-cf am-padding">
      <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">消息通知</strong> / <small>OMG 你的通知</small></div>
    </div>

    <hr>

    <div class="am-g" >
     <!-- am-in 控制扩展与否 -->

     <div class="am-panel-group color-panel-group" id="accordion1" style="margin-left:25px;">
         <div class="am-panel am-panel-default color-panel-border">
             <div class="am-panel-hd">
                 <h4 class="am-panel-title am-text-danger " data-am-collapse="{parent: '#accordion1', target: '#do-not-say-4'}"><i class="am-icon-laptop"></i> 系统通知消息<span class="color-panel-helper">Get System Info</span>
                     <div class="tool">
                        <a href="javascript:;" class="am-icon-chevron-down am-fr"> </a>
                     </div>
                 </h4>
             </div>
             <div id="do-not-say-4" class="am-panel-collapse am-collapse am-in">
                   <?php if($system_message_view){ ?>
                   <div class="am-panel-bd " style="margin-right:25px;">
                     <ul class="am-comments-list admin-content-comment" style="margin-bottom:5px;">
                       <li class="am-comment">
                         <div class="am-comment-main">
                           <header class="am-comment-hd">
                             <div class="am-comment-meta"><a href="#" class="am-comment-author"></a>
                               <h3 class="am-comment-title">老师</h3>
                             </div>
                           </header>
                           <div class="am-comment-bd">
                             <p>
                               <?php echo $system_message_view['post_content']; ?>
                             </p>
                           </div>
                          </div>
                        </li>
                        <?php } else {?>
                          <div class="am-panel-bd">欢迎使用默云学习助手！</br></div>
                        <?php }?>
                       </ul>
                   </div>
             </div>
         </div>
     	</div>


   <br>
   <br>
      <?php if($logged_admin == false && $logged_teacher == false) { ?>
      <div class="am-panel-group color-panel-group" id="accordion2" style="margin-left:25px;">
      <div class="am-panel am-panel-default color-panel-border">
        <div class="am-panel-hd">
              <h4 class="am-panel-title am-collapsed  am-text-success" data-am-collapse="{parent: '#accordion2', target: '#do-not-say-5'}"><i class="am-icon-book"></i> 作业完成状态<span class="color-panel-helper">Get HomeWork Info</span>
                  <div class="tool">
                      <a href="javascript:;" class="am-icon-chevron-down am-fr"> </a>
                  </div>
              </h4>
          </div>
          <div id="do-not-say-5" class="am-panel-collapse am-collapse" style="height: 0px;">
            <div class="am-panel-bd " style="margin-right:25px;">
              <ul class="am-comments-list admin-content-comment" style="margin-bottom:5px;">
                <li class="am-comment">
                  <div class="am-comment-main">
                    <header class="am-comment-hd">
                      <div class="am-comment-meta"><a href="#" class="am-comment-author"></a>
                        <h3 class="am-comment-title">作业完成状态</h3>
                      </div>
                    </header>
                    <div class="am-comment-bd">
                      <p>
                        <p>您还有<?php echo $tip_task_user_row; ?>项作业没有完成<?php if($tip_task_user_row>0){ ?>，请尽快完成哦！<?php }else{ echo '；'; } ?></p>
                        <p>您已经完成了<?php echo $task_user_count; ?>个课堂作业；</p>
                        <p>学习任务中有<?php echo $task_count; ?>个课堂作业等待完成。</p>
                      </p>
                    </div>
                   </div>
                 </li>
                </ul>
            </div>
      </div>
      </div>
  </div>

      <br>
      <br>

<div class="am-panel-group color-panel-group" id="accordion3" style="margin-left:25px;">
<div class="am-panel am-panel-default color-panel-border">
  <div class="am-panel-hd">
        <h4 class="am-panel-title am-collapsed  am-text-primary" data-am-collapse="{parent: '#accordion3', target: '#do-not-say-6'}"><i class="am-icon-area-chart"></i> 作业成绩展示<span class="color-panel-helper">Get Score Info</span>
            <div class="tool">
                <a href="javascript:;" class="am-icon-chevron-down am-fr"> </a>
            </div>
        </h4>
    </div>
    <div id="do-not-say-6" class="am-panel-collapse am-collapse" style="height: 0px;">
      <div class="am-panel-bd" style="margin-right:25px;">
        <ul class="am-comments-list admin-content-comment" style="margin-bottom:5px;">
          <li class="am-comment">
            <div class="am-comment-main">
              <header class="am-comment-hd">
                <div class="am-comment-meta"><a href="#" class="am-comment-author"></a>
                  <h3 class="am-comment-title">作业成绩</h3>
                </div>
              </header>
              <div class="am-comment-bd">
                <p>
                  <p>本次作业成绩：<?php echo $performance_mouth_count; ?></p>
                  <p>学期作业总成绩：<?php echo $performance_count; ?></p>
                </p>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

     <?php } ?>

      <br>
      <br>
      <!-- 4 -->
  <div class="am-panel-group color-panel-group" id="accordion4" style="margin-left:25px;">
  <div class="am-panel am-panel-default color-panel-border">
    <div class="am-panel-hd">
          <h4 class="am-panel-title am-collapsed  am-text-warning" data-am-collapse="{parent: '#accordion4', target: '#do-not-say-7'}"><i class="am-icon-commenting-o"></i> 最近私信留言<span class="color-panel-helper">Get Message Info</span>
              <div class="tool">
                  <a href="javascript:;" class="am-icon-chevron-down am-fr"> </a>
              </div>
          </h4>
      </div>
      <div id="do-not-say-7" class="am-panel-collapse am-collapse" style="height: 0px;">
        <div class="am-panel-bd" style="margin-right:25px;">
        <ul class="am-comments-list admin-content-comment" style="margin-bottom:5px;">
          <?php if($message_list){ foreach($message_list as $v){ $v_view = $oapost->view($v['id']); $v_user = $moyunuser->view_user($v['post_user']); ?>
          <li class="am-comment">
            <div class="am-comment-main">
              <header class="am-comment-hd">
                <div class="am-comment-meta"><a href="#" class="am-comment-author"><?php if($v_user){ echo $v_user['user_name']; } ?></a></div>
              </header>
              <div class="am-comment-bd"><p><?php if($v_view){ echo $v_view['post_content']; unset($v_view); } ?></p></div>
            </div>
          </li>
          <?php } }else{ ?>
          <li class="am-comment">
              <div class="am-comment-bd">(lll￢ω￢)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;没有任何消息</div>
          </li>
          <?php } ?>
        </ul>
      </div>
    </div>
  </div>
</div>


 </div>
 <br>
 <br>
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
