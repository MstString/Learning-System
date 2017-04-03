<?php
/**
 * 用户组管理页面
 * @author string.zhengyang@gmail.com
 * @version 2
 * @package MOYUN
 */
if (isset($init_page) == false) {
    die();
}

/**
 * 初始化变量
 */
$page = 1;
if(isset($_GET['page']) == true){
    $page = $_GET['page'];
}
$max = 10;

/**
 * 获取用户组列表记录数
 */
$group_list_row = $moyunuser->get_group_row();

/**
 * 计算页码
 */
if($page < 1){
    $page = 1;
}
$page_max = ceil($group_list_row/$max);
if($page > $page_max){
    $page = $page_max;
}
$page_prev = $page-1;
$page_next = $page+1;

/**
 * 获取用户组列表
 */
$group_list = $moyunuser->view_group_list($page);
?>
<div class="admin-content">
    <div class="admin-content-body">

      <div class="am-cf am-padding">
        <div class="am-fl am-cf">
          <strong class="am-text-primary am-text-lg">用户组管理</strong> / <small>User Groups</small>
        </div>
      </div>

      <hr>

      <div class="am-g">
        <div class="am-u-sm-12">
          <form class="am-form">
            <table class="am-table am-table-bordered am-table-hover color-table-bordered">
            <thead>
            <tr>
              <th class="table-author"><span class="am-icon-list"></span> ID</th>
              <th><span class="am-icon-users"></span> 用户组名称</th>
              <th><span class="am-icon-hourglass"></span> 权限</th>
              <th><span class="am-icon-crop"></span> 状态</th>
              <th><span class="am-icon-gear"></span> 操作</th>
            </tr>
            </thead>
            <tbody id="group_list">
              <?php if($group_list){ foreach($group_list as $k=>$v){ ?>
              <tr>
                  <td><?php echo $v['id']; ?></td>
                  <td><?php echo $v['group_name']; ?></td>
                  <td><?php if($v['group_power'] == 'admin'){ echo '管理员'; }else{ echo '普通用户'; } ?></td>
                  <td><?php echo $v['group_status'] ? '正常':'已禁用'; ?></td>
                  <td>
                    <div class="btn-group">
                    <button href="#group_edit" type="button" role="button" class="am-btn color-btn" data-am-modal="{target: '#group_edit'}"><i class="icon-pencil"></i> 编辑</button>
                    <button href="#del" type="button" class="am-btn color-btn" data-am-popover="{theme:'warning', content: 'That is OK！', trigger: 'hover focus'}"><i class="icon-trash icon-white"></i> 删除</button>
                    </div>
                  </td>
              </tr>
              <?php } } ?>
              <tr class="info">
                  <td></td>
                  <td><div class="input-prepend"><span class="add-on"><i class="icon-th"></i></span><input type="text" id="add_name" placeholder="组名称"></div></td>
                  <td><div class="input-prepend"><span class="add-on"><i class="icon-briefcase"></i></span><select id="add_power"><option value="admin">管理员</option><option value="teacher">教师</option><option value="normal">普通用户</option></select></div></td>
                  <td>启用</td>
                  <td><button href="#add" type="button" class="am-btn color-btn" data-am-popover="{theme:'warning', content: 'That is OK！', trigger: 'hover focus'}" type="button"><i class="icon-plus icon-white"></i> 添加</button></td>
              </tr>
            </tbody>
          </table>
        </form>

        <div class="am-cf">
          <ul class="am-pagination am-default admin-content-pagination" style="padding-left: 25px;">
            <li class="am-<?php if($page<=1){ echo ' disabled'; } ?>" style="float:left;"><a href="<?php echo $page_url.'&page='.$page_prev; ?>">&laquo;上一页</a></li>
            <li class="am-<?php if($page>=$page_max){ echo ' disabled'; } ?>" style="float:right; margin-right:25px;"><a href="<?php echo $page_url.'&page='.$page_next;?>">下一页&raquo;</a></li>
          </ul>
        </div>

        <div class="am-modal am-modal-prompt" tabindex="-1" id="group_edit">
          <div class="am-modal-dialog">
            <div class="am-modal-hd">编辑用户组</div>
            <div class="am-modal-bd">
                <div class="control-group">
                  <label class="control-label" for="edit_username">组名</label>
                  <div class="controls">
                      <div class="input-prepend">
                          <span class="add-on"><i class="icon-users"></i></span>
                          <input type="text" id="edit_name" placeholder="用户名">
                      </div>
                  </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="edit_power">权限</label>
                    <div class="controls">
                        <div class="input-prepend">
                            <span class="add-on"><i class="icon-star"></i></span>
                            <select id="edit_power"><option value="admin">管理员</option><option value="normal">教师</option><option value="normal">普通用户</option></select>
                        </div>
                    </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="edit_status">状态</label>
                  <div class="controls">
                      <div class="input-prepend">
                          <span class="add-on"><i class="icon-info-sign"></i></span>
                          <select id="edit_status"><option value="1">启用</option><option value="0">禁用</option></select>
                      </div>
                  </div>
                </div>
            </div>
            <hr>
            <div class="am-modal-footer">
              <button style="width:50%;" class="am-modal-btn" data-am-modal-cancel>取消</button>
              <button style="width:50%;" href="#edit_save" type="submit" class="am-modal-btn am-button" data-am-modal-confirm>提交</button>
            </div>
            </div>
      </div>
      </div>
    </div>

    </div>
</div>
<!-- Javascript -->
<script>
    $(document).ready(function(){
        //添加按钮事件
    $("button[href='#add']").click(function(){
        $.post("ajax_user_group.php",{
            "add_name":$("#add_name").val(),
            "add_power":$("#add_power").val()
        },function(data){
            msg(data,"添加成功！","无法添加新的用户组，请检查您输入的用户组名称、权限是否正确！");
            tourl(1500,"init.php?init=16");
        });
    });

    //删除按钮事件
    $("button[href='#del']").click(function(){
        var ev = $(this).parent().parent().parent().children();
        $("#group_list").data("del",$(ev).html());
       $.get("ajax_user_group.php?del="+$("#group_list").data("del"),function(data){
           msg(data,"删除成功！","无法删除该用户组，请确保系统至少存在一个用户组，同时您不能删除系统默认组！");
           if(data=="2"){
               $(ev).parent("tr").remove();
           }
       });
    });

    //编辑按钮事件
    $("button[href='#group_edit']").click(function(){
        var ev = $(this).parent().parent().parent();
        $("#group_edit").data("edit_x",ev);
        $("#group_edit").data("edit_id",$(ev).children().html());
        $("#group_edit").data("edit_name",$(ev).children("td:eq(1)").html());
        $("#edit_name").val($(ev).children("td:eq(1)").html());
        var power = $(ev).children("td:eq(2)").html();
        if(power=="管理员"){
            $("#edit_power").val("admin");
        }else{
            $("#edit_power").val("normal");
        }
        var status = $(ev).chilrdren("td:eq(3)").html();
        if(status=="正常"){
            $("#edit_status").val("1");
        }else{
            $("#edit_status").val("0");
        }
    });

    //编辑保存按钮事件
    $("button[href='#edit_save']").click(function(){
        $.post("ajax_user_group.php",{
            "edit_id":$("#group_edit").data("edit_id"),
            "edit_name":$("#edit_name").val(),
            "edit_power":$("#edit_power").val(),
            "edit_status":$("#edit_status").val()
        },function(data){
            msg(data,"修改成功！","无法修改用户组，请检查您输入的用户组名称或权限是否正确！");
            if(data=="2"){
                var ev = $("#group_edit").data("edit_x");
                $(ev).children("td:eq(1)").html($("#edit_name").val());
                var power = $("#edit_power").val();
                var power_str = "";
                if(power=="admin"){
                    power_str = "管理员";
                }else{
                    power_str = "普通用户";
                }
                $(ev).children("td:eq(2)").html(power_str);
                var status = $("#edit_status").val();
                var status_str = "";
                if(status=="1"){
                    status_str = "正常";
                }else{
                    status_str = "已禁用";
                }
                $(ev).children("td:eq(3)").html(status_str);
            }
        });
        $("#group_edit").modal('hide');
    });
    });
</script>
