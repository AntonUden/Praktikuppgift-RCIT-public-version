<?php
class article {
	protected $id;
	protected $title;
	protected $text;
	protected $category;
	protected $author;
	protected $date;
	protected $image;
	protected $edited;
	
	function getID() {
		return $this->id;
	}

	function getTitle() {
		return $this->title;
	}

	function getText() {
		return $this->text;
	}

	function getCategory() {
		return $this->category;
	}

	function getAuthor() {
		return $this->author;
	}

	function getDate() {
		return $this->date;
	}
	
	function getImage() {
		return $this->image;
	}

	function isEdited() {
		return $this->edited;
	}

	function __construct($id = null, $title = null, $text = null, $category = null, $author = null, $date = null, $image = null, $edited = null) {
		$this->id = $id;
		$this->title = $title;
		$this->text = $text;
		$this->category = $category;
		$this->author = $author;
		$this->date = $date;
		$this->image = $image;
		$this->edited = $edited;
	}
}
?>