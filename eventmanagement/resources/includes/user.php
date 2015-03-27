<?php
	class User {
		public $id;
		public $email;
		public $access = 0;
		public $activated = false;
		
		public function __construct($id, $mail, $acc, $activated)
		{
			$this->id = $id;
			$this->email = $mail;
			$this->access = $acc;
			$this->activated = $activated;
		}
	}
?>