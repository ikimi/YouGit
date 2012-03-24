<?php
class AdminModel extends PeopleModel {

		private static $instance;

		public static function getInstance($name) {
			if(empty(self::$instance)) {
				self::$instance = new AdminModel();
				self::$instance->truename =  $name;
			}
			return self::$instance;
		}

		public function haha() {echo 'AdminModel';}
	}
?>
