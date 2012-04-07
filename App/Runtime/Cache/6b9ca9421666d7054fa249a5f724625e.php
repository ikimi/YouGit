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
	<h4><?php echo ($info["commit_msg"]); ?></h4>
	<h7><?php echo ($info["commitor"]); ?> <?php echo ($info["time"]); ?></h7><br>
	parent <a href="__APP__/Repository/commit?commit=<?php echo ($info["parent"]); ?>"><?php echo ($info["parent"]); ?></a> commit <a href="__APP__/Repository/commit?commit=<?php echo ($info["SHA_1"]); ?>"><?php echo ($info["SHA_1"]); ?></a>
	<br>-----------------------------------------<br>
	<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><?php echo ($vo["file"]); ?>&nbsp<?php echo ($vo["info"]); ?><br><?php endforeach; endif; else: echo "" ;endif; ?>
	<?php echo ($msg); ?>
	<br>-----------------------------------------<br>
</body>
</html>