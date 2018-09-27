<?php
/**
* Class that handles all connections and requests to the database
*/
class db_connection {
	private $servername = "localhost";
	private $username = "root";
	private $password = "root";
	private $dbname = "artikelsida";
	
	protected $conn;

	//Reference is required for PHP 5.3+
	function refValues($arr) {
		if (strnatcmp(phpversion(),'5.3') >= 0) {
			$refs = array();
			foreach($arr as $key => $value)
				$refs[$key] = &$arr[$key];
			return $refs;
		}
		return $arr;
	}

	// Returns the mysql connection object
	function getConnection() {
		return $this->conn;
	}

	// Selects the db to use
	function selectDB($db) {
		$this->conn->select_db($db);
	}

	// Executes a mysql statement and returns array with 'success', 'result', 'insert_id', 'errno' and 'error'
	function exec_statement($query) {
		$stmt = $this->conn->prepare($query);
		
		if($stmt == false) {
			return array('success' => false,'result' => null, 'insert_id' => null, 'errno' => 1, 'error' => "Failed");
		}

		// add params to statement
		if(func_num_args() > 2) {
			$args = array_slice(func_get_args(), 1);
			call_user_func_array(array($stmt, 'bind_param'), $this->refValues($args));
		}

		$result = array('success' => $stmt->execute(),'result' => $stmt->get_result(), 'insert_id' => $stmt->insert_id, 'errno' => $stmt->errno, 'error' => $stmt->error);

		return $result;
	}

	function __construct() {
		$this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
		if ($this->conn->connect_error) {
			die("Connection failed: " . $this->conn->connect_error);
		}
		mysqli_set_charset($this->conn,"utf8");
	}
}
?>