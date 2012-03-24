<?php
	class CommonAction extends Action {
		public function _initialize() {
			if(!isset($_COOKIE['username']))
				$this->redirect('Index/login');
		}
	}
?>
