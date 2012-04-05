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
			echo $path;
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
		$data['reponame'] = $project;

		if(($workship->data($data)->add())&&($repo->insert($project,$description,$homepage)))
			return true;
		else
			return false;
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
				if($this->config($_POST['project']))
					$this->push();
				$this->redirect("index?proName={$_POST['project']}");
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
	protected function config($project) {

		//打开权限配置文件
		if(!($file = fopen("/var/www/YouGit/gitosis-admin/gitosis.conf","a+"))) {
			return false;
		}

		//先增加一个换行
		fwrite($file,"\n[group ");
		fwrite($file,time()."]\n");

		//增加配置信息
		fwrite($file,"members = {$this->visitor->keyname}\n");
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
