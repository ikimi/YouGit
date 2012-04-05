<?php if (!defined('THINK_PATH')) exit();?><html xmlns="http://www.w3.org/1999/xhtml" >
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>YouGit</title>
</head>
<body>
	<a href='__APP__/Settings/profile'>Account Settings</a><br>
	<a href='__APP__/Index/logout'>Logout</a><br>
	<a href='__APP__/Repository/create'>Create Repository</a><br>
<h5><?php echo ($info["project"]); ?></h5>
<a href="http://<?php echo ($info["homepage"]); ?>"><?php echo ($info["homepage"]); ?></a><br>
<?php echo ($info["description"]); ?><br>
<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><a href="__APP__/Repository/repository?<?php echo ($vo["path"]); ?>"><?php echo ($vo["name"]); ?></a><br><?php endforeach; endif; else: echo "" ;endif; ?>
</body>
</html>