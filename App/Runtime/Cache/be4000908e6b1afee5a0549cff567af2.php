<?php if (!defined('THINK_PATH')) exit();?><html xmlns="http://www.w3c.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>YouGit</title>
	</head>
	<body>
		<a href='profile'>Profile</a><br>
		<a href='ssh'>SSH Key</a><br>
		<a href='admin'>Account Settings</a><br>
		<h5><?php echo ($error); ?></h5>
		<h5>Change Password</h5>
		<form name="admin" method="post" action="admin">
			Old Password:<br><input name="old" type="password" size="15"/><br>
			New Password:<br><input name="new" type="password" size="15"/><br>
			Confirm New Password:<br><input name="confirm" type="password" size="15"/><br>
			<input name="submit" type="submit" value="Reset Password"><br>
		</form>
	</body>
</html>