<?php
require $_SERVER['DOCUMENT_ROOT'].'/php/db_connection.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/obj_category.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/obj_author.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/obj_article.php';
/**
* Class that contains functions to get data from the database
*/
class db_data_manager extends db_connection {
	protected $connection;

	// Returns the connection object
	function getConnection() {
		return $this->connection;
	}

	// Return array with authors
	function getAuthorList() {
		$queryResult = $this->connection->exec_statement("SELECT * FROM `forfattare`");
		if($queryResult['success']) {
			$result = array();
			while ($d=mysqli_fetch_assoc($queryResult['result'])) {
				array_push($result, new author($d['id'], $d['namn'], $d['email']));
			}
			return $result;
		}
		return array();
	}

	// returns author by id
	function getAuthorByID($id) {
		$queryResult = $this->connection->exec_statement("SELECT * FROM `forfattare` WHERE id = ?", "s", $id);
		if($queryResult['success']) {
			if($queryResult['result']->num_rows > 0) {
				$d=mysqli_fetch_assoc($queryResult['result']);
				return new author($d['id'], $d['namn'], $d['email']);
			}
		}
		return new author(null, null, null);
	}

	// returns author by email
	function getAuthorByEmail($email) {
		$queryResult = $this->connection->exec_statement("SELECT * FROM `forfattare` WHERE email = ?", "s", $email);
		if($queryResult['success']) {
			if($queryResult['result']->num_rows > 0) {
				$d=mysqli_fetch_assoc($queryResult['result']);
				return new author($d['id'], $d['namn'], $d['email']);
			}
		}
		return new author(null, null, null);
	}

	// Creates or updates a author with name and email
	function createOrUpdateAuthor($name, $email) {
		if($this->getAuthorByEmail($email)->getID()) {
			$this->connection->exec_statement("UPDATE forfattare SET namn= ? WHERE email = ?", "ss", $name, $email);
		} else {
			$this->connection->exec_statement("INSERT INTO `forfattare` (`id`, `email`, `namn`) VALUES (NULL, ?, ?)", "ss", $email, $name);
		}
	}

	// Returns a list with all categories
	function getCategoryList() {
		$queryResult = $this->connection->exec_statement("SELECT * FROM `kategori`");
		if($queryResult['success']) {
			$result = array();
			while ($d=mysqli_fetch_assoc($queryResult['result'])) {
				array_push($result, new category($d['id'], $d['kategorinamn']));
			}
			return $result;
		} else {
			return array();
		}
	}

	// Checks if a category exists
	function categoryExists($name) {
		$queryResult = $this->connection->exec_statement("SELECT * FROM `kategori` WHERE kategorinamn=?", "s", $name);
		if($queryResult['success']) {
			if($queryResult['result']->num_rows > 0) {
				return true;
			}
		}
		return false;
	}

	// returns category by id
	function getCategoryByID($id) {
		$queryResult = $this->connection->exec_statement("SELECT * FROM `kategori` WHERE id = ?", "s", $id);
		if($queryResult['success']) {
			if($queryResult['result']->num_rows > 0) {
				$d=mysqli_fetch_assoc($queryResult['result']);
				return new category($d['id'], $d['kategorinamn']);
			}
		}
		return new category(null, null);
	}

	// Creates a category
	function createCategory($name) {
		if($this->categoryExists($name)) {
			return true;
		}
		$queryResult = $this->connection->exec_statement("INSERT INTO `kategori` (`id`, `kategorinamn`) VALUES (NULL, ?)", "s", $name);
		if($queryResult['success']) {
			return true;
		}
		return false;
	}

	// Deletes a category
	function deleteCategory($id) {
		$queryResult = $this->connection->exec_statement("DELETE FROM `kategori` WHERE id = ?", "s", $id);
		if($queryResult['success']) {
			return true;
		}
		return false;
	}


	// returns article by id
	function getArticleByID($id) {
		$queryResult = $this->connection->exec_statement("SELECT * FROM `artikel` WHERE id = ?", "s", $id);
		if($queryResult['success']) {
			if($queryResult['result']->num_rows > 0) {
				$d=mysqli_fetch_assoc($queryResult['result']);
				return new article($d['id'], $d['titel'], $d['text'], $this->getCategoryByID($d['kategoriid']), $this->getAuthorByID($d['forfattarid']), $d['datum'], $d['bild'], $d['redigerad']);
			}
		}
		return false;
	}

	// Checks if a article with $title and $text exists
	function articleExists($title, $text) {
		$queryResult = $this->connection->exec_statement("SELECT count(0) FROM artikel WHERE titel=? AND text=?", "ss", $title, $text);
		if($queryResult['success']) {
			if(intval(mysqli_fetch_assoc($queryResult['result'])["count(0)"]) > 0) {
				return true;
			}
		}
		return false;
	}

	// Returns a list with articles
	function getArticles($count, $offset, $category, $author) {
		if($category && !$author) {
			$queryResult = $this->connection->exec_statement("SELECT * FROM artikel WHERE kategoriid=? ORDER BY id DESC LIMIT ? OFFSET ?", "sii", $category, $count, $offset);
		} else if($author && !$category) {
			$queryResult = $this->connection->exec_statement("SELECT * FROM artikel WHERE forfattarid=? ORDER BY id DESC LIMIT ? OFFSET ?", "sii", $author, $count, $offset);
		} else if($author && $category) {
			$queryResult = $this->connection->exec_statement("SELECT * FROM artikel WHERE kategoriid=? && forfattarid=? ORDER BY id DESC LIMIT ? OFFSET ?", "ssii", $category, $author, $count, $offset);
		} else {
			$queryResult = $this->connection->exec_statement("SELECT * FROM artikel ORDER BY id DESC LIMIT ? OFFSET ?", "ii", $count, $offset);
		}

		if($queryResult['success']) {
			$result = array();
			while ($d=mysqli_fetch_assoc($queryResult['result'])) {
				array_push($result, new article($d['id'], $d['titel'], $d['text'], $this->getCategoryByID($d['kategoriid']), $this->getAuthorByID($d['forfattarid']), $d['datum'], $d['bild'], $d['redigerad']));
			}
			return $result;
		} else {
			return array();
		}
	}

	// Counts articles by author or category
	public function countArticles($category, $author) {
		if($category && !$author) {
			$result = $this->connection->exec_statement("SELECT count(0) FROM artikel WHERE kategoriid=?", "s", $category);
		} else if($author && !$category){
			$result = $this->connection->exec_statement("SELECT count(0) FROM artikel WHERE forfattarid=?", "s", $author);
		} else if($author && $category) {
			$result = $this->connection->exec_statement("SELECT count(0) FROM artikel WHERE kategoriid=? && forfattarid=?", "ss", $category, $author);
		} else {
			$result = $this->connection->exec_statement("SELECT count(0) FROM artikel");
		}
		if($result['success']) {
			return intval(mysqli_fetch_assoc($result['result'])["count(0)"]);
		}
		return 0;
	}

	// Deletes a article by $id
	public function deleteArticleByID($id) {
		$queryResult = $this->connection->exec_statement("DELETE FROM artikel WHERE id=?", "s", $id);
		if($queryResult['success']) {
			return true;
		}
		return false;
	}

	function __construct() {
		$this->connection = new db_connection;
	}
}
?>