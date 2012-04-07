<?php if (!defined('THINK_PATH')) exit();?><html xmlns="http://www.w3.org/1999/xhtml" >
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>YouGit</title>
</head>
<body>
	<a href='__APP__/Settings/profile'>Account Settings</a><br>
	<a href='__APP__/Index/logout'>Logout</a><br><br>
	<a href='__APP__/Repository/admin'>admin</a><br>
	<br>-----------------------------------------<br>
	<a href="__APP__/Repository/index?project=<?php echo ($project); ?>">Files</a>&nbsp<a href='__APP__/Repository/commits'>Commits</a>
	<br>-----------------------------------------<br>
	<h5><?php echo ($path); ?></h5>
	<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?>~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	<h4><?php echo ($vo["commit_msg"]); ?></h4>
	<h7><?php echo ($vo["commitor"]); ?> authored at <?php echo ($vo["time"]); ?></h7>
	<h5><a href="__APP__/Repository/commit?commit=<?php echo ($vo["SHA_1"]); ?>"><?php echo ($vo["SHA_1"]); ?></a></h5><?php endforeach; endif; else: echo "" ;endif; ?>
</body>
</html>