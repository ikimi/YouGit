<?php if (!defined('THINK_PATH')) exit();?><html xmlns="http://www.w3.org/1999/xhtml" >
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>YouGit</title>
</head>
<body>
	<h5>Welcome <?php echo ($username); ?></h5>
	<a href='__APP__/Settings/profile'>Account Settings</a><br>
	<a href='__APP__/Index/logout'>Logout</a><br>
	<a href='__APP__/Repository/create'>Create Repository</a><br>
<h5>Email <?php echo ($Info["email"]); ?><br>
Location <?php echo ($Info["location"]); ?><br>
Member Since <?php echo ($Info["membersince"]); ?><br></h5>
<?php if(is_array($repos)): $i = 0; $__LIST__ = $repos;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$repo): ++$i;$mod = ($i % 2 )?>----------------------------------------------------------
<h4><a href="__APP__/Repository/index?project=<?php echo ($repo["info"]["project"]); ?>"><?php echo ($repo["info"]["project"]); ?></a></h4>
<?php echo ($repo["info"]["description"]); ?><br>
<?php echo ($repo["info"]["homepage"]); ?><br><br>
<?php if(is_array($repo['msg'])): $i = 0; $__LIST__ = $repo['msg'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$msg): ++$i;$mod = ($i % 2 )?><?php echo ($msg["commitor"]); ?> pushed to master at <?php echo ($repo["info"]["project"]); ?> <?php echo ($msg["time"]); ?><br>
<?php echo ($msg["commit_msg"]); ?><br><?php endforeach; endif; else: echo "" ;endif; ?>
----------------------------------------------------------<?php endforeach; endif; else: echo "" ;endif; ?>
</body>
</html>