<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<!-- saved from url=(0036)http://www.luoliang.com.cn/-->
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<style type="text/css" media="screen">
body,html {background:#fff;font-family: "微软雅黑","Lucida Grande",Calibri,Arial;font-size: 13pt;color: #333;background: #f8f8f8;}
tr.e {background:#f4f4f4;}
th,td {padding:0.1em 0.5em;}
th {text-align:left;font-weight:bold;background:#eee;border-bottom:1px solid #aaa;}
#top1 {width:80%; font-size:28px; margin: 0 auto 5px auto;}
#top2 {width:80%; font-size:18px; margin: 0 auto 5px auto;}
#footer {width:80%;margin: 0 auto; padding: 10pt 0;font-size: 10pt;text-align:center;}
#footer a {font-size: 14px; font-weight: normal; text-decoration: underline;}
#list {border:1px solid #aaa;width:80%;margin: 0 auto;padding: 0;}
a {color: royalblue;font-size: 11pt;text-decoration: none;}
a:hover {color: royalblue; background: gainsboro;}
#readme {padding:0;margin:1em 0;border:none;width:100%;}
</style>
<script type="text/javascript">// <![CDATA[function ngx_onload(){var f=document.getElementById('readme');if(!(f&&f.contentDocument))return;f.style.height=f.contentDocument.body.offsetHeight+'px';f.contentDocument.body.style.padding='0';f.contentDocument.body.style.margin='0';}// ]]></script>

<?php
header("content-type","text/html;charset=utf-8");
 /*
 *统计目录大小
 *@param string $dirName 目录名
 *@return double
 */
 function dirSize($dirName)
 {$dir_size = 0;
     if($dir_handle = @opendir($dirName))
     { while ($fileName = readdir($dir_handle))
         {/*排除两个特殊目录*/
             if($fileName != '.' && $fileName != '..')
             {$subFile = $dirName.'/'.$fileName;
                 if(is_file($subFile))
                 {$dir_size += filesize($subFile);}
                 if(is_dir($subFile))
                 {$dir_size += dirSize($subFile);}}}
         closedir($dir_handle);
         return $dir_size;}}
 /*传递当前目录下的test目录*/
//   $dir_size = dirSize('./');
//   echo '<p style="font-size:14pt;text-align:center">在'.$subFile.'目录文件大小总计：'.round($dir_size / pow(1024,2),2).' MB';
/*遍历目录*/
   $num=0;    //用来记录目录下的文件个数
   $dirname='./'; //要遍历的目录名字
   $dir_handle=opendir($dirname); ////打开目录取得目录句柄

   echo '<table border="1" align="center" width="50%" cellspacing="0" cellpadding="0">';
   echo '<caption><h2>目录'.$dirname.'的内容</h2></caption>';
   echo '<tr align="left" bgcolor="#cccccc">';
   echo '<th>序号↓</a></th><th><a href="?C=N&O=A">名称↓</a></th><th><a href="?C=N&O=A">大小↓</a></th><th><a href="?C=N&O=A">类型↓</a></th><th>修改时间↓</th></tr>';
   while($file=readdir($dir_handle)) //用readdir读取当前打开的目录,并赋值给$file.
   {if($file!="."&&$file!="..") //假如$file的值不为. 或者..则
   	 {$dirFile=$dirname."/".$file;
   	 	if($num++%2==0)    //隔行换色
   	 		$bgcolor="#ffffff";
   	 	else
   	 		$bgcolor="#cccccc";
   	 	echo '<tr bgcolor='.$bgcolor.'>';
   	 	echo '<td>'.$num.'</td>';//序号
   	 	echo '<td><a href="'.$file.'" target="_blank">'.$file.'</a></td>'; //名称 链接
//   	 	echo '<td>'.round(filesize($dirFile) / 1024,2).' MB</td>';//大小
//echo '<td>'.filesize($dirFile).'</td>';
//{   	 	if('.filesize($dirFile).'>=1024*1024) //假如大于1M则用M表示小于则用k表示
//   	 	{echo  '<td>'.round(filesize($dirFile)/(1024*1024),2)." MB";//大小MB
//   	 	}else
//   	 	{echo  '<td>'.round(filesize($dirFile)/1024 ,2)." KB";}//大小KB
//}

if('.filesize($dirFile).'>=1024*1024*1024) //假如大于1M则用M表示小于则用k表示
{
    echo  '<td>'.round(filesize($dirFile)/(1024*1024*1024),2)." GB";
}elseif('.filesize($dirFile).'>=1024*1024)
{
    echo  '<td>'.round(filesize($dirFile)/(1024*1024),2)." MB";
}else
{
    echo  '<td>'.round(filesize($dirFile)/1024,2)." KB";
}

   	 	echo '<td>'.filetype($dirFile).'</td>';//类型
   	 	echo '<td>'.date("Y/n/t",filemtime($dirFile)).'</td>';//修改时间
   	 	echo '</tr>';}}
   echo '</table>';
   closedir($dir_handle); //关闭句柄
   $dir_size = dirSize($dirname);//目录大小
   echo '<p style="font-size:14pt;text-align:center">在<b>'.$dirname.'</b>目录下的子目录和文件共有<b>'.$num.'</b>个 占空间';

//假如大于1M则用M表示小于则用k表示
//if($dir_size>=1024*1024)
//{echo  '('.$subFile.'总计)：'.round($dir_size/pow(1024,2),2) . " M";//0=bytes 1=kb 2=mb 3=tb
//}else
//{echo  '('.$subFile.'总计)：'.round($dir_size/pow(1024,1),2). " K";}//0=bytes 1=kb 2=mb 3=tb

if($dir_size>=1024*1024*1024) //假如大于1M则用M表示小于则用k表示
{
    echo round($dir_size/(1024*1024*1024),2) . " GB";
}elseif($dir_size>=1024*1024)
{
    echo round($dir_size/(1024*1024) ,2). " MB";
}else
{
    echo round($dir_size/1024 ,2). " KB";
}


?>
<div id="footer">
  <p><a href="#" target="_blank">MOYUN</a>(修改)&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" onclick="scrollTo(0,0);" title="返回顶部">TOP</a></p>
  </div>
</body>

</html>