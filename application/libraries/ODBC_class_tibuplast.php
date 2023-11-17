<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ODBC_class_tibuplast {

   // ------- Constructor ------- //
	function __construct() {
		$this->user = "Tibuplast";
		$this->pass = "Maatwerk";			
		$this->source = "KingDataDigger_Tibuplas";
		self::connect();
	}
	
	// ------- Connect to ODBC ------- //
	public function connect($source = NULL) {
		if ($source != NULL){
			$this->source = $source;
		}
		$this->connection = odbc_connect(
			$this->source,
			$this->user,
			$this->pass
		) or die ("Kan niet inloggen, controleer credentials");
		 //echo "INGELOGD";
	}
	
	// ------- Query ------- //
	function query($sql) {
		$query = odbc_exec(
			$this->connection, 
			$sql
		) or die (odbc_error()  );
		return $query;
	}
	
	// ------- Row count ------- //
	function num_rows($sql) {
		return odbc_num_rows(self::query($sql));
	}
	
	// ------- Result ------- //
	function results($sql) {
		$count = 0;
		$data = array();
		$res =  self::query($sql);
		while ($line = @odbc_fetch_object($res)) {
			$data[$count] = $line;
			$count++;
		}
		@odbc_free_result($res);
		return $data;
	}
	
	// ------- Disconnect from ODBC ------- //
	function disconnect() {
		odbc_close(
			$this->connection
		);
	}
	
	// ------- Destructor ------- //
	function __destruct() {
		$this->source;
		$this->user;
		$this->pass;
	}
}