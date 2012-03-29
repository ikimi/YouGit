<?php

//定义项目裸仓库根目录
define('ROOT_PATH','/var/www/YouGit/repo/');

//日志类
class Log {

	//项目裸仓库根目录
	public $dir;

	//项目总数
	public $repo_num;

	//项目信息列表
	public $repo_list;
	//构造函数，初始化
	public function __construct($dir) {
		$this->dir = $dir;
		$this->repo_num = 0;
		$this->object_num = 0;
		$this->repo_list = array();
	}

	//获取项目总数
	public function repoNum() {
		if(is_dir($this->dir)) {

			//搜索每一个目录
			$repos = scandir($this->dir);
			$repo_num = 0;

			foreach($repos as $repo) {
				if($repo != '.' && $repo != '..')
					$repo_num++;
			}
			$this->repo_num = $repo_num;
		}
	}

	//获取列表信息
	public function repoList() {
		if(is_dir($this->dir)) {
			
			//搜索每一个目录
			$repos = scandir($this->dir);
			$list = array();

			foreach($repos as $repo) {
				if($repo != '.' && $repo != '..') {

					//objects对象监测目录
					$objects_dir = ROOT_PATH.$repo.'/objects/';
					$objects = scandir($objects_dir);

					$list[$repo] = array();
					//进入该项目objects目录下
					foreach($objects as $object_tmp) {
						if($object_tmp != '.' && $object_tmp != '..' && 
							$object_tmp != 'info' && $object_tmp != 'pack') {
							$files = scandir($objects_dir.$object_tmp);

							//进入某次object目录下
							foreach($files as $object) {
								if($object != '.' && $object != '..') {
									$list[$repo][] = $object_tmp.$object;
								}
							}
						}
					}
				}
			}
			$this->repo_list = $list;
		}
	}
}
?>
