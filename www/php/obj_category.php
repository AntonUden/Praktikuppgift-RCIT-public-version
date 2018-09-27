<?php 
/**
* Category object
*/
class category {
	protected $id;
	protected $name;

	public function getID() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	function __construct($id = null, $name = null) {
		$this->id = $id;
		$this->name = $name;
	}
}
?>