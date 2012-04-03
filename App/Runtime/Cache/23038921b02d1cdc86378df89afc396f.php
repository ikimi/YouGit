<?php if (!defined('THINK_PATH')) exit();?><html xmlns="http://www.w3c.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>YouGit &rsaquo; Regist</title>
	</head>
	<body>
		<form name="regist" method="post" action="">
			Username:<input name="username" type="text" size="15"/><br>
			E-mail:<input name="email" type="text" size="15"/><br>
			Location:<input name="location" type="text" size="15"/><br/>
			Password:<input name="password" type="password" size="15"/><br>
			Confirm Password:<input name="confirm" type="password" size="15"/><br>
			<input name="submit" type="submit" value="Regist"><br>
		</form>
		<input name="login" type="button" value="返回" onclick="window.location='login'">
	</body>
</html>