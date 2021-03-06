<?php
class RepositoryAction extends CommonAction {

	private $repoPath = '/var/www/YouGit/Repositories';
	/**
	 * ----------------------------------------------------------
	 * 项目首页
	 * ----------------------------------------------------------
	 */
	public function index() {
		
		/*
		 * 判断项目是否为空	
		 * 若为空 则显示固定页面
		 * 若不为空 则显示项目信息
		 */
		if(isset($_COOKIE['project']) && ($_COOKIE['project'] != $_GET['project'])) {
			unset($_COOKIE['project']);
		}
		setcookie('project',$_GET['project']);
		$repo = RepositoryModel::getInstance($_GET['project']);
		$empty = $repo->isEmpty();

		if($empty['empty']) {
			$visitor = UserModel::getInstance($_COOKIE['username']);
			$data = $visitor->where("username='{$visitor->getName()}'")->field('name,email')->find();
			$data['project'] = $_GET['project'];
			$data['ip'] = $_SERVER['REMOTE_ADDR'];

			$this->assign('data',$data);
			$this->display('default');
		}
		else {

			// 显示项目文件列表
			$info = $repo->getInfo();
			$this->assign('info',$info);
			$path = $this->repoPath.'/'.$_GET['project'].'.git/';
			$list = scandir($path);
			array_splice($list,0,2);

			$data = array();
			foreach($list as $value) {
				if(is_file($path.$value)) {
					$temp['path'] = "project={$_GET['project']}&blob=$value";
				}
				else
					$temp['path'] = "project={$_GET['project']}&tree=$value/";
					$temp['name'] = $value;
					$data[] = $temp;
				}
			$this->assign('data',$data);
			$this->display('index');
		}
	}

	/**
	 * ----------------------------------------------------------
	 * 返回缓存项目内容
	 * ----------------------------------------------------------
	 */
	public function repository() {

		if(isset($_GET['tree']))
			$path = $this->repoPath.'/'.$_GET['project'].'.git/'.$_GET['tree'];
		if(isset($_GET['blob']))
			$path = $this->repoPath.'/'.$_GET['project'].'.git/'.$_GET['blob'];

		// 如果是文件 返回文件内容
		if(is_file($path)) {
			$content = file_get_contents($path);
			$this->assign('content',$content);
			//$this->display('');
			echo $content;
		}

		// 如果是目录 返回目录内内容列表
		elseif(is_dir($path)) {
			$list = scandir($path);
			array_splice($list,0,2);

			$pos = strrpos($_GET['tree'],'/');
			$tree = substr($_GET['tree'],0,$pos);
			if($tree == '') {
				$this->redirect("index?project={$_GET['project']}");
				return;
			}
			$temp['path'] = "project={$_GET['project']}&tree=$tree";
			$temp['name'] = '..';
			$data[] = $temp;

			foreach($list as $value) {
				if(is_file($path.$value))
					$temp['path'] = "project={$_GET['project']}&blob={$_GET['tree']}$value";
				else
					$temp['path'] = "project={$_GET['project']}&tree={$_GET['tree']}$value/";
				$temp['name'] = $value;
				$data[] = $temp;
			}
			$this->assign('project',$_GET['project']);
			$this->assign('path',$_GET['project'].'/'.$_GET['tree']);
			$this->assign('data',$data);
			$this->display('repository');
		}
	}

	/**
	 * ----------------------------------------------------------
	 * 更新workship表
	 * ----------------------------------------------------------
	 */
	public function workship($project,$description='',$homepage='') {
		if(empty($project))
			return false;

		$repo = RepositoryModel::getInstance($project);

		$workship = M('workship');
		$data['username'] = $_COOKIE['username'];
		$data['project'] = $project;

		if(($workship->data($data)->add())&&($repo->insert($project,$description,$homepage)))
			return true;
		else
			return false;
	}

	/**
	 * ----------------------------------------------------------
	 * 项目属性
	 * ----------------------------------------------------------
	 */
	public function admin() {
		$project = $_COOKIE['project'];
		if(empty($_POST)) {
			$repo = RepositoryModel::getInstance($project);
			$info = $repo->getInfo();

			$this->assign('info',$info);
			$this->display("admin");
		}
		else {
			$data['description'] = $_POST['description'];
			$data['homepage'] = $_POST['homepage'];
			$repo = RepositoryModel::getInstance($project);
			$repo->where("project='{$repo->getName()}'")->save($data);
			$this->redirect('admin');
		}
	}

	/**
	 * ----------------------------------------------------------
	 * 给项目添加贡献者
	 * ----------------------------------------------------------
	 */
	public function collaborators() {
		if(empty($_POST)) {
			$visitor = UserModel::getInstance($_COOKIE['username']);
			$userList = $visitor->field('username')->select();
			$this->assign('userList',$userList);
			$this->display('collaborators');
		}
		else {

			// 获得要添加的用户
			$workship = M('workship');
			$data = array();
			$data['project'] = $_COOKIE['project'];

			// 将信息存到 workship 表中
			foreach($_POST['collaborators'] as $value) {
				$data['username'] = $value;
				$workship->data($data)->add();
				$visitor = UserModel::getInstance($_COOKIE['username']);
				$key = $visitor->getSshkey();
				if($this->config($key['keyTitle'],$_COOKIE['project']))
					$this->push();	
			}
			$this->redirect('admin');
		}
	}

	/**
	 * ----------------------------------------------------------
	 * 项目提交历史
	 * ----------------------------------------------------------
	 */
	public function commits () {
		$repo = RepositoryModel::getInstance($_COOKIE['project']);
		$this->assign('project',$_COOKIE['project']);

//		var_dump($repo->getUpdate());
		$this->assign('data',$repo->getUpdate());
		$this->display('commits');
	}

	/**
	 * ----------------------------------------------------------
	 * 此次提交的更改信息
	 * ----------------------------------------------------------
	 */
	public function commit() {
		$commit = $_GET['commit'];

		$com = M('commit');
		$project = $_COOKIE['project'];
		$info = $com->where("SHA_1='$commit'")->field('SHA_1,parent,commitor,time,commit_msg')->find();
		$father = $info['parent'];
		$result = array();
		/*
		 *  执行 git diff --stat 脚本
		 *  project 项目名称
		 *  father 旧提交
		 *  commit 新提交
		 *  0 不加 --stat 选项
		 *  1 加 --stat 选项
		 */
		exec("/bin/bash /var/www/YouGit/script/diff.sh $project $father $commit 1",$result);
		$len = count($result);
		$data = array();
		for($i = 0;$i < $len-1;$i++) {
			list($temp['file'],$temp['info']) = explode('|',$result[$i]);
			$temp['file'] = trim($temp['file']);
			$temp['info'] = trim($temp['info']);
			$data[] = $temp;
		}
		$msg = $result[$len-1];
		// 文件差异对比
		unset($temp);
		unset($result);
		$result = array();
		exec("/bin/bash /var/www/YouGit/script/diff.sh  $project $father $commit 0",$result);

		// 获得更改文件的对比信息
		$files = array();
		$len = count($result);
		for($i = 0;$i < $len;) {
			if(substr($result[$i],0,10) == 'diff --git') {
				if(!empty($temp)) {
					$files[] = $temp;
					unset($temp);
				}

				// 获得文件名
				$filename = split(' ',substr($result[$i++],13));
				$temp['filename'] = $filename[0];

				// 是否是新文件
				if(substr($result[$i],0,5) == 'index') {
					$temp['status'] = 'modified';
				}
				elseif(substr($result[$i],0,8) == 'new file') {
					$temp['status'] = 'added';
					$i++;
				}
				$temp['SHA_1'] = substr($result[$i++],-7);
				$i += 2;
			}
			$temp['content'] .= $result[$i++];
		}
		if(!empty($temp))
			$files[] = $temp;

		$this->assign('project',$_COOKIE['project']);
		$this->assign('info',$info);
		$this->assign('data',$data);
		$this->assign('msg',$msg);
		$this->assign('files',$files);
		$this->display("commit");
	}

	/**
	 * ----------------------------------------------------------
	 * 浏览文件
	 * ----------------------------------------------------------
	 */
	public function blob() {
		$blob = $_GET['blob'];
		$result = array();
		exec("/bin/bash /var/www/YouGit/script/cat_file.sh {$_COOKIE['project']}.git $blob 2",$result);
		foreach($result as $value)
			echo $value.'<br>';
	//	var_dump($result);
	}

	/**
	 * ----------------------------------------------------------
	 * 项目创建
	 * ----------------------------------------------------------
	 */
	public function create() {
		if(empty($_POST['project']))
			$this->display('create');
		else {
			if($this->workship($_POST['project'],$_POST['description'],$_POST['homepage'])) {
				$this->bare($_POST['project']);
				$visitor = UserModel::getInstance($_COOKIE['username']);
				$key = $visitor->getSshkey();

				if($this->config($key['keyTitle'],$_POST['project']))
					$this->push();
				$this->redirect("index?project={$_POST['project']}");
			}
			else {

				//创建项目失败
				echo 'failed';
			}
		}
	}

	/**
	 * ----------------------------------------------------------
	 * 初始化项目
	 * ----------------------------------------------------------
	 */
	protected function bare($project) {
		exec("/bin/bash /var/www/YouGit/script/bare.sh $project.git",$result);
	}
	
	/**
	 * ----------------------------------------------------------
	 * 设置gitosis-admin.git 项目管理权限
	 * ----------------------------------------------------------
	 */
	protected function config($key,$project) {

		//打开权限配置文件
		if(!($file = fopen("/var/www/YouGit/gitosis-admin/gitosis.conf","a+"))) {
			return false;
		}

		//先增加一个换行
		fwrite($file,"\n[group ");
		fwrite($file,time()."]\n");

		//增加配置信息
		fwrite($file,"members = $key\n");
		fwrite($file,"writable = $project\n");
		
		return true;
			
	}

	/**
	 * ----------------------------------------------------------
	 * push项目更新
	 * ----------------------------------------------------------
	 */
	protected function push() {

		$commit_msg = time();
		exec("/bin/bash /var/www/YouGit/script/push.sh $commit_msg");
	}

	/**
	 * ----------------------------------------------------------
	 * 项目删除
	 * ----------------------------------------------------------
	 */
		public function remove() {
			
		}
	}
?>
