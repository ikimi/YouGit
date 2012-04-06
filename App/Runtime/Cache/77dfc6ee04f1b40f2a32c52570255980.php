<?php if (!defined('THINK_PATH')) exit();?><html xmlns="http://www.w3c.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>YouGit</title>
	</head>
	<body>
		<a href='profile'>Profile</a><br>
		<a href='ssh'>SSH Key</a><br>
		<a href='admin'>Account Settings</a><br>
		<form name="collaborators" method="post" action="collaborators">
			<?php if(is_array($userList)): $i = 0; $__LIST__ = $userList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><input  name='collaborators[]' type='checkbox' value="<?php echo ($vo["username"]); ?>"/><?php echo ($vo["username"]); ?><br><?php endforeach; endif; else: echo "" ;endif; ?>
			<input name="submit" type="submit" value="Add"><br>
		</form>
	</body>
</html>