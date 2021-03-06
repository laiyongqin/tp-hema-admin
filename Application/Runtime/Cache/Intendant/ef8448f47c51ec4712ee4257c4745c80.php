<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>菜单管理</title>
    <link href="/public/admin/css/frame.css" rel="stylesheet">
    <link href="/public/common/js/poshytip/src/tip-darkgray/tip-darkgray.css" rel="stylesheet">
    <style type="text/css">
    .tip-darkgray{z-index: 99999;}
    </style>
</head>
<body>
<div id="frame-top">
    当前位置&nbsp;<i class="iconfont" style="color:#666;font-size: 12px;">&#xe60e;</i>&nbsp;&nbsp;设置&nbsp;&nbsp;<i class="iconfont" style="color:#666;font-size: 12px;">&#xe60f;</i>&nbsp;&nbsp;站长设置&nbsp;&nbsp;<i class="iconfont" style="color:#666;font-size: 12px;">&#xe60f;</i>&nbsp;&nbsp;菜单设置
</div>
<div id="site-menu-upload" style="display:none;"></div>
<div id="frame-toolbar">
    <ul>
        <li><a class="active" href="/intendant/Site/menu"><i class="iconfont" style="color:white;font-size: 16px;">&#xe611;</i>&nbsp;&nbsp;菜单设置</a></li>
        <li><a href="/intendant/Site/addEditMenu"><i class="iconfont" style="color:white;font-size: 16px;">&#xe610;</i>&nbsp;&nbsp;添加菜单</a></li>
        <li><a id="export" class="bsn" title="菜单导出的位置是C盘/用户/当前系统用户/下载" href="/intendant/Site/exportMenu"><i class="iconfont" style="color:white;font-size: 16px;">&#xe611;</i>&nbsp;&nbsp;导出菜单</a></li>
        <li><a id="import" href="/intendant/Site/importMenu"><i class="iconfont" style="color:white;font-size: 16px;">&#xe611;</i>&nbsp;&nbsp;导入菜单</a></li>
    </ul>
</div>
<div id="frame-content">
<form name="commonSort" class="ajax-form" action="/intendant/Site/sortMenu" method="post">
    <div class="frame-table-list">
        <table width="100%">
            <colgroup>
                <col width="40">
                <col width="40">
                <col width="240">
                <col width="160">
                <col width="80">
                
                <col width="160">
            </colgroup>
            <thead>
            <tr>
                <td align="center">排序</td>
                <td align="center">ID</td>
                <td align="left">菜单名称</td>
                <td align="center">控制器/方法</td>               
                <td align="center">是否显示</td>
                <td align="center">管理操作</td>
            </tr>
            </thead>
            <tbody>
                <?php if(is_array($menu)): $i = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><tr>
                    <td align="center"><input style="text-align: center" name="sort[<?php echo ($menu['id']); ?>]" type="text" size="3" value="<?php echo ($menu['sort']); ?>" class="input"></td>
                    <td align="center"><?php echo ($menu['id']); ?></td>
                    <td><span class="tree-icon tree-file icons-calendar-calculator_edit"></span><!-- <i class="org-2">1</i> --><?php echo str_repeat('&nbsp;&nbsp;&nbsp;',$menu['level']); echo ($menu['html']); ?><i class="iconfont icon" style=""><?php echo ($menu['icon']); ?></i><?php echo ($menu['title']); ?></td>
                    <td align="center"><?php echo ($menu['name']); ?></td>
                    <td align="center"><?php if($menu["isshow"] == 1): ?><i class="iconfont" style="color:green;font-size: 16px;">&#xe60c;</i><?php else: ?><i class="iconfont" style="color:red;font-size: 16px;">&#xe60a;</i><?php endif; ?></td>
                    <td align="center"><a class="ajax-add blue" href="/Intendant/Site/addEditMenu/?pid=<?php echo ($menu['id']); ?>" data-level="<?php echo ($menu['level']); ?>"><i class="iconfont" style="color:white;font-size: 16px;">&#xe610;</i>&nbsp;&nbsp;添加子菜单</a>&nbsp;&nbsp;&nbsp;<a class="ajax-edit blue" href="/Intendant/Site/addEditMenu/?id=<?php echo ($menu['id']); ?>"><i class="iconfont" style="color:white;font-size: 16px;">&#xe615;</i>&nbsp;&nbsp;修改</a>&nbsp;&nbsp;&nbsp;<a class="ajax-del red" data-title="<?php echo ($menu['title']); ?>" data-type="菜单" href="/Intendant/Site/delMenu" data-id="<?php echo ($menu['id']); ?>"><i class="iconfont" style="color:white;font-size: 16px;">&#xe614;</i>&nbsp;&nbsp;删除</a> </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
    </div>
    <div class="frame-table-btn">
        <button class="btn ajax-sort" type="submit">排序</button>
    </div>
</form>
</div>
</body>
<script src="/public/common/js/jquery/jquery-1.12.3.min.js"></script>
<script src="/public/common/js/Huploadify/jquery.Huploadify.js"></script>
<script src="/public/common/js/layer/layer.js"></script>
<script src="/public/admin/js/admin.common.js"></script>
<script src="/public/admin/js/common.ajax.js"></script>
<script src="/public/common/js/poshytip/src/jquery.poshytip.min.js"></script>
<script type="text/javascript">
//上传插件
$('#site-menu-upload').Huploadify({
    auto:true,
    fileTypeExts:'*.data',
    multi:false,
    formData:null,
    fileSizeLimit:1024,
    showUploadedPercent:false,
    showUploadedSize:false,
    removeTimeout:1000,
    uploader:'<?php echo U("Site/importMenu");?>',
    onUploadSuccess : function(file, data, response) {
        var data = JSON.parse(data);
        if(!data.status){
            admin.alert("提示信息", data.info,2, '3000');
        }else{
            admin.alert("提示信息", data.info,1, '3000');
            setTimeout(function () {
                admin.reloadPage();
            }, 3000);             
        }
    },
    'onUploadError' : function(file, errorCode, errorMsg, errorString) {            
        admin.alert("提示信息", file.name + ' 上传失败。详细信息: ' + errorString,2, '3000');
    }
});
</script>
<script>
$(function(){    
    $('.ajax-add').on('click', function (e) {
        var data = $(this).attr('data-level');
        if(data >= 3){e.preventDefault();admin.alert('提示信息','对不起，菜单最多4级！',2,'3000');}
    });    
    //导出菜单
    $('#export').on('click', function (e) {
        e.preventDefault();
        var btn = $(this),
            submit = btn.attr('href');
            myload = layer.load(0,{time:100*1000});
        $.post(submit,'',function(data){
            layer.close(layer.load(1));
            if(!data.status){
                admin.alert("提示信息", data.info,2, '3000');
            }else{
                admin.countdown(3);
                admin.alert('操作提示', data.info+'<div>程序将在<b style="color:red;" id="second_show">03秒</b>后为你跳转！</div>', 1, '3000');
                location.href = data.url;             
            }
        },'json').error(function(){
            layer.close(layer.load(1));
            admin.alert('提示信息',data.responseText,1,'3000');
        });
    });
    //导入菜单
    $('#import').on('click', function (e) {
        e.preventDefault();
        layer.confirm('该操作将清空所有数据，请备份好数据之后再进行导入，确定要继续吗？', {icon: 3,offset:'200px', title:'导入菜单提示'}, function(index){
            layer.close(index);
            $("#site-menu-upload .uploadify-button").click();
        });
    });
    
});
</script>
</html>