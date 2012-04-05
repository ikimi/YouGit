<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Create Reporitory</title>
</head>
<body>
<form name="create" method="post" action="create">
	Project:<br><input name="project" type="text" size="15" /><br>
	Desctiption(Optional):<br><input name="description" type="text" size="15"><br>
	Homepage uRL(Optional):<br><input name="homepage" type="text" size="15"><br>
	<input name="submit" type="submit" value="Create repository">
</form>
</body>
</html>