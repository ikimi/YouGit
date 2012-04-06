<?php

	public $truename = '';

	public $keyname = '';

	/**
	 * ----------------------------------------------------------
	 * 创建项目
	 * ----------------------------------------------------------
	 */
	public function create($project) {
		$repo = new RepositoryModel();
		$participate = M('participate');

		$data['reponame'] = $project;
		$data['username'] = $this->truename;

		//判断项目是否添加成功`
	//	if(($repo->data($data)->add()) && 
	//		($participate->data($data)->add()))
			return true;
	//	else 
	//		return false;

	}

	public function haha() {
		echo "PeopleModel";
	}	
}
?>
