<?php 
/**
* Author object
*/
class author {
	protected $id;
	protected $name;
	protected $email;

	public function getID() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function getEmail() {
		return $this->email;
	}

	function __construct($id = null, $name = null, $email = null) {
		$this->id = $id;
		$this->name = $name;
		$this->email = $email;
	}
}
?>