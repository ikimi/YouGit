<?php
class UserModel extends Model {

		private static $instance;

		private $rawname = '';

		private $key = '';

		// 单例模式生成对象
		public static function getInstance($name) {
			if(empty(self::$instance)) {
				self::$instance = new UserModel();
				self::$instance->setName($name);
			}
			return self::$instance;
		}

		public function setName($name) {
			$this->rawname = $name;
		}

		public function getName() {
			return $this->rawname;
		}
		
		public function getInfo() {

			// 仅返回e-mail，注册时间，所在地
			$info = $this->where("username='{$this->getName()}'")->field('name,email,membersince,location')->find();
			return $info;
		}

		public function getProfile() {
			$profile = $this->where("username='{$this->getName()}'")->field('name,email,blog,company,location')->find();
			return $profile;
		}
		
		public function setProfile($profile) {
			if(empty($profile))
				return false;
			$this->where("username='{$this->getName()}'")->save($profile);
			return true;
		}
		
		public function getPassword() {
			$password = $this->where("username='{$this->getName()}'")->getField('password');
			return $password;
		}

		public function setPassword($password,$confirm) {
			if(strlen($password) < 6) {
				setcookie('error','Passwords needs at least 6 words~',0);
				return false;
			}
			elseif($password !== $confirm) {
				setcookie('error','Password is not equal to Confirm Password~',0);
				return false;
			}
			else {
				$data['password'] = $password;
				$this->where("username='{$this->getName()}'")->save($data);
				setcookie('error','Reset Success~',0);
				return true;
			}
		}

		public function getSshkey() {
			$Key = $this->where("username='{$this->getName()}'")->field('keyTitle,key')->find();
			return $Key;
		}

		public function setSshkey($title,$key) {

			// STEP 1:更新数据库
			$data['keyTitle'] = $title;
			$data['key'] = $key;
			$this->where("username='{$this->getName()}'")->save($data);

			// STEP 2:更新keydir文件夹下面的文件
			$file = "/var/www/YouGit/gitosis-admin/keydir/$title";
			file_put_contents($file,$key);
		}

		// 获取用户所有参加的项目
		public function getRepos() {
			
			// 在合作表中获取
			$workship = M('workship');
			$repos = $workship->where("username='{$this->getName()}'")->field('project')->select();
			$data = array();
			foreach($repos as $repo) {
				$Repo = RepositoryModel::getInstance($repo['project']);
				unset($temp);
				$temp['info'] = $Repo->getInfo();
				$temp['msg'] = $Repo->getUpdate();
				$data[] = $temp;
			}
			var_dump($data);
			return $data;
		}
	}
?>
