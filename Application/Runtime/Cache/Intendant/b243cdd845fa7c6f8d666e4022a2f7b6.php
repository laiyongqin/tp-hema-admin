<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>设置用户名</title>
    <link href="/public/common/js/zTree/css/zTreeStyle/zTreeStyle.css"  rel="stylesheet" />
    <link href="/public/admin/css/frame.css" rel="stylesheet">
</head>
<body>
<div id="frame-top">
    当前位置&nbsp;<i class="iconfont" style="color:#666;font-size: 12px;">&#xe60e;</i>&nbsp;&nbsp;设置&nbsp;&nbsp;<i class="iconfont" style="color:#666;font-size: 12px;">&#xe60f;</i>&nbsp;&nbsp;用户设置&nbsp;&nbsp;<i class="iconfont" style="color:#666;font-size: 12px;">&#xe60f;</i>&nbsp;&nbsp;权限设置
</div>
<div id="frame-toolbar">
    <ul>
        <li><a href="/Intendant/Site/role"><i class="iconfont" style="color:white;font-size: 16px;">&#xe611;</i>&nbsp;&nbsp;角色管理</a></li>
        <li><a href="/Intendant/Site/addEditRole"><i class="iconfont" style="color:white;font-size: 16px;">&#xe610;</i>&nbsp;&nbsp;添加角色</a></li>
    </ul>
</div>
<div id="frame-content">
    <form name="commonGroup" class="ajax-form" action="/Intendant/Site/setRole" method="post">
        <input type="hidden" name="setRole" value="setRole" />
        <input type="hidden" name="id" value="<?php echo ($id); ?>" />
        <input type="hidden" name="ruleid" value="" />
        <div class="frame-table-list">
            <table width="100%">
                <thead>
                    <div class="title"><?php echo ($title); ?>&nbsp;-&nbsp;角色权限设置</div>
                </thead>
                <tbody>
                    <div class="table-tree">
                        <ul id="treeRule" class="ztree">
                        </ul>
                    </div>
                </tbody>
            </table>
        </div>
        <div class="frame-table-btn">
            <button class="btn ajax-role" type="submit">授权</button>
        </div>
    </form>
</div>
</body>
<script src="/public/common/js/jquery/jquery-1.12.3.min.js"></script>
<script src="/public/common/js/layer/layer.js"></script>
<script src="/public/admin/js/admin.common.js"></script>
<script src="/public/admin/js/common.ajax.js"></script>
<script src="/public/common/js/zTree/js/jquery.ztree.core-3.5.min.js"></script>
<script src="/public/common/js/zTree/js/jquery.ztree.excheck-3.5.min.js"></script>
<script src="/public/common/js/poshytip/src/jquery.poshytip.min.js"></script>
<script>
    //配置
    var setting = {
        check: {
            enable: true,
            chkboxType:{ "Y" : "ps", "N" : "ps" }
        },
        data: {
            simpleData: {
                enable: true,
                idKey: "id",
                pIdKey: "pid",
            }
        },
        callback: {
            beforeClick: function (treeId, treeNode) {
                if (treeNode.isParent) {
                    zTree.expandNode(treeNode);
                    return false;
                } else {
                    return true;
                }
            },
            onClick:function(event, treeId, treeNode){
                //栏目ID
                var catid = treeNode.catid;
                //保存当前点击的栏目ID
                setCookie('tree_catid',catid,1);
            }
        },
        view:{
            nameIsHTML: true
        }
    };
    //节点数据
    var zNodes = <?php echo ($json); ?>;
    $(function(){
        $.fn.zTree.init($("#treeRule"), setting, zNodes);
        // setCheck();
        // 	$("#py").bind("change", setCheck);
        // 	$("#sy").bind("change", setCheck);
        // 	$("#pn").bind("change", setCheck);
        // 	$("#sn").bind("change", setCheck);
        $.fn.zTree.init($("#treeRule"), setting, zNodes);
        zTree = $.fn.zTree.getZTreeObj("treeRule");
        zTree.expandAll(true);
        // });
        $('button.ajax-role').on('click', function (e) {
            e.preventDefault();
            var btn = $(this), form = btn.parents('form.ajax-form');
            if($('button.btn').attr("disabledSubmit")){
                $('button.btn').text('请勿重复提交...').prop('disabled', true).addClass('disabled');
                return false;
            }
            $('button.btn').attr("disabledSubmit",true);
            form.find('input[name="ruleid"]').val("");
            var  nodes = zTree.getCheckedNodes(true);
            var str = "";
            $.each(nodes,function(i,value){
                if (str != "") {
                    str += ",";
                }
                str += value.id;
            });
            form.find('input[name="ruleid"]').val(str);
            var param = form.serialize();
            $.ajax({
                url: form.attr('action'),
                dataType:'json',
                type:'POST',
                data:param,
               beforeSend: function(){
                    myload = layer.load(0,{time:3*1000});
                },
                success: function(data){
                    layer.close(layer.load(1));
                    if(!data.status){
                        admin.alert('提示信息',data.info,2,'3000');
                        $('button.btn').text('授权').removeProp('disabled').removeClass('disabled');
                        $('button.btn').attr("disabledSubmit",'');
                    } else {
                        admin.countdown(3);
                        admin.alert('提示信息',data.info+'<div>程序将在<b style="color:red;" id="second_show">03秒</b>后为你跳转！</div>',1,'3000');
                        setTimeout(function(){
                            admin.reloadPage();
                        },3000);
                    }
                },
                error: function(data){
                    layer.close(layer.load(1));
                    admin.alert('提示信息',data.responseText,1,'3000');
                }
            });
        });
    });
</script>
</html>