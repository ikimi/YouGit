<?php if (!defined('THINK_PATH')) exit();?><html xmlns="http://www.w3.org/1999/xhtml" >
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>YouGit &rsaquo; Login</title>
</head>
<body>
<h4><?php echo ($error); ?></h4>
<form name="login" method="post" action="">
	用户名:<input name="username" type="text" size="15" /><br/>
	密&nbsp码:<input name="password" type="password" size="15" /><br/>
	<input name="submit" type="submit" value="Login">
</form>
</body>
</html>