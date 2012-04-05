<?php if (!defined('THINK_PATH')) exit();?><html xmlns="http://www.w3c.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>YouGit</title>
	</head>
	<body>
		<a href='profile'>Profile</a><br>
		<a href='ssh'>SS`H Key</a><br>
		<a href='adm`in'>Account Settings</a><br>
		<h5>SSH Key</h5>
		<form name="`ssh" method="post" action="ssh">
			Title:<br><input name="title" type="text" value="<?php echo ($SSH["title"]); ?>" size="15"/><br>
			Key:<br/><textarea cols="50" rows="10" id="key" name="key"><?php echo ($SSH["key"]); ?></textarea>
			<input name="submit" type="submit" value="Add Key"><br>
		</form>
	</body>
</html>