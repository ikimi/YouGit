<?php if (!defined('THINK_PATH')) exit();?><html xmlns="http://www.w3c.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>YouGit</title>
	</head>
	<body>
		<a href='profile'>Profile</a><br>
		<a href='ssh'>SSH Key</a><br>
		<a href='admin'>Account Settings</a><br>
		<form name="profile" method="post" action="profile">
			Name:<br><input name="name" type="text" value="<?php echo ($Profile["name"]); ?>" size="15"/><br>
			E-mail:<br><input name="email" type="text" value="<?php echo ($Profile["email"]); ?>" size="15"/><br>
			website/blog:<br><input name="blog" type="text" value="<?php echo ($Profile["blog"]); ?>" size="15"/><br>
			Company:<br><input name="company" type="text" value="<?php echo ($Profile["company"]); ?>" size="15"/><br>
			Location:<br><input name="location" type="text" value="<?php echo ($Profile["location"]); ?>" size="15"/><br/>
			<input name="submit" type="submit" value="Save"><br>
		</form>
	</body>
</html>