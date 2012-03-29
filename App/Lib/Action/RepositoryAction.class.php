<?php
class RepositoryAction extends CommonAction {

	protected $visitor;

	/**
	 * ----------------------------------------------------------
	 * 初始化---反序列化People对象
	 * ----------------------------------------------------------
	 */
	public function _initialize() {
		$this->visitor = unserialize($_COOKIE['visitor']);
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
			if($this->visitor->create($_POST['project'])) {
					$this->bare($_POST['project']);
				if($this->config($_POST['project']))
					$this->push();
			//在server端初始化项目
					echo 'success';	
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
