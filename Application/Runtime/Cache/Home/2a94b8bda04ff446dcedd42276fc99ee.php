<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>河马CMS首页</title>
	<link rel="stylesheet" href="/public/home/css/common.css" />
	<link rel="stylesheet" href="/public/common/js/unslider/dist/css/unslider.css">
	<style>
	
	</style>
</head>
<body>
<div id="hm_top">
	<div class="warp">
		<div class="logo">
			<a href="/"><img src="/public/home/img/logo.png" alt=""></a>
		</div>
		<div class="nav">
			<div class="item"><a href="#">首页</a></div>
			<div class="item" onmouseover="this.className='item hover'" onmouseout="this.className='item'">
				<div class="title"><a href="#">资料</a><i class="iconfont" style="color:white;font-size: 18px;">&#xe600;</i></div>				
				<div class="cc">
					<div class="warp">
						<ul>
							<li>
								<h2><a href=""><i class="iconfont" style="color:white;font-size: 24px;">&#xe601;</i>&nbsp;你好</a></h2>
								<ol>
									<li><a href="1"><i class="iconfont" style="color:white;font-size: 16px;">&#xe601;</i>&nbsp;撒旦发射点发撒旦发射</a></li>
									<li><a href="2"><i class="iconfont" style="color:white;font-size: 16px;">&#xe601;</i>&nbsp;撒旦发射点发撒旦发射</a></li>
									<li><a href="3"><i class="iconfont" style="color:white;font-size: 16px;">&#xe601;</i>&nbsp;撒旦发射点发撒旦发射</a></li>
									<li><a href=""><i class="iconfont" style="color:white;font-size: 16px;">&#xe601;</i>&nbsp;撒旦发射点发撒旦发射</a></li>
									<li><a href=""><i class="iconfont" style="color:white;font-size: 16px;">&#xe601;</i>&nbsp;撒旦发射点发撒旦发射</a></li>
									<li><a href=""><i class="iconfont" style="color:white;font-size: 16px;">&#xe601;</i>&nbsp;撒旦发射点发撒旦发射</a></li>
								</ol>
							</li>
							<li>
								<h2><a href=""><i class="iconfont" style="color:white;font-size: 24px;">&#xe601;</i>&nbsp;你好</a></h2>
								<ol>
									<li><a href="">你好</a></li>
									<li><a href="">你好</a></li>
									<li><a href="">你好</a></li>
								</ol>
							</li>
							<li>
								<h2><a href=""><i class="iconfont" style="color:white;font-size: 24px;">&#xe601;</i>&nbsp;你好</a></h2>
								<ol>
									<li><a href="">你好</a></li>
									<li><a href="">你好</a></li>
									<li><a href="">你好</a></li>
								</ol>
							</li>
							<li>
								<h2><a href=""><i class="iconfont" style="color:white;font-size: 24px;">&#xe601;</i>&nbsp;你好</a></h2>
								<ol>
									<li><a href="">你好</a></li>
									<li><a href="">你好</a></li>
									<li><a href="">你好</a></li>
								</ol>
							</li>
						</ul>
					</div>
				</div>
			</div>
			
		</div>
	</div>
</div>
<div id="hm_slider" style="margin-top:61px;width:100%;background:#272822;">
	<div class="warp">
		<div class="banner" style="overflow-y:hidden">
	      	<ul>
		        <li style="background-image: url(http://img20.360buyimg.com/da/jfs/t3040/268/636348886/102764/3160ea19/57abde25N4b6ef8d9.jpg); ">		        	
			        <a href="" class="zl"></a>			            
		        </li>
		        <li style="background-image: url(http://img20.360buyimg.com/da/jfs/t3040/268/636348886/102764/3160ea19/57abde25N4b6ef8d9.jpg); ">
		        	<div>
		        		<h1>继续更新</h1>
			            <a href="" class="btn">下载</a>
			            <a href="" class="btn">下载</a>
		        	</div>		            
		        </li>
		        <li style="background-image: url(http://img20.360buyimg.com/da/jfs/t3040/268/636348886/102764/3160ea19/57abde25N4b6ef8d9.jpg); ">
		        	<div>
		        		<h1>继续更新</h1>
			            <a href="" class="btn">下载</a>
			            <a href="" class="btn">下载</a>
		        	</div>		            
		        </li>
	      	</ul>
	 	</div> 
 	</div>
</div>
<div style="width:100%;background:white;border-bottom:1px solid red;">
	<div class="warp" style="padding:50px;padding-top:80px;padding-bottom:80px;">
		<ul class="idTabs intabs" style="width:100%;margin:0 auto;">
		  <li><a href="#jquery"><i class="iconfont">&#xe605;</i></a><h2>通用后台</h2></li>
		  <li><a href="#official"><i class="iconfont">&#xe602;</i></a><h2>微信营销</h2></li>
		  <li><a href="#jquery"><i class="iconfont">&#xe607;</i></a><h2>电子商务</h2></li>
		  <li><a href="#official"><i class="iconfont">&#xe604;</i></a><h2>其他产品</h2></li>
		</ul>		
	</div>
	<div class="warp" style="padding:50px;">
		<div style="width:100%;height:500px;border:1px solid #ededed;" id="jquery">If you haven't checked out ...</div>
		<div style="width:100%;" id="official">idTabs is only a simple ...</div>
	</div>
</div>
</body>
<script src="/public/common/js/jquery/jquery-1.12.3.min.js"></script>
<!-- <script src="//code.jquery.com/jquery-latest.min.js"></script> -->
<script src="/public/common/js/unslider/dist/js/unslider-min.js"></script>
<script src="/public/common/js/jquery.idTabs.min.js"></script>
<script>

$(function(){
	var cc = $(window).width();
	$('.cc').css('width',cc+17);
	var slidey = $('.banner').unslider({
        speed: 500,               
		  delay: 3000,            
		  complete: function() {}, 
		  keys: true,             
		  dots: true,              
		  fluid: false
	});
});

</script>

</html>