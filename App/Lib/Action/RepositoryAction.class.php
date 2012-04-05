<?php
class RepositoryAction extends CommonAction {

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
		$repo = RepositoryModel::getInstance($_GET['proName']);
		$empty = $repo->isEmpty();
		if($empty['empty']) {
			$visitor = UserModel::getInstance($_COOKIE['username']);
			$data = $visitor->where("username='{$visitor->getName()}'")->field('name,email')->find();
			$data['project'] = $_GET['proName'];
			$data['ip'] = $_SERVER['REMOTE_ADDR'];

			$this->assign('data',$data);
			$this->display('default');
		}
		else {

			// 检索项目列表，展示所有项目
			$info = $repo->getInfo();
			var_dump($info);
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
