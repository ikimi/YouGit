<?php
class RepositoryModel extends Model {
	
	private static $instance;

	private $proName;

	public static function getInstance($project='') {
		if(empty(self::$instance)) {
			self::$instance = new RepositoryModel();
		}
		self::$instance->setName($project);
		return self::$instance;
	}	
	
	public function setName($proName='') {
		$this->proName = $proName;
	}

	public function getName() {
		return $this->proName;
	}

	// 将项目信息存储到数据库中
	public function insert($project,$description='',$homepage='') {

		$data['project'] = $project;
		$data['description'] = $description;
		$data['homepage'] = $homepage;
		$data['empty'] = '1';

		var_dump($data);
		if($this->data($data)->add())
			return true;
		return false;
	}

	// 返回项目是否为空
	public function isEmpty() {
		return $this->where("project='{$this->getName()}'")->field('empty')->find();
	}

	// 返回所有项目列表
	public function getInfo() {
		return $this->where("project='{$this->getName()}'")->field('project,description,homepage')->find();
	}

}
?>
