<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#[AllowDynamicProperties]
class ODBC_class_tibuplast {

   // ------- Constructor ------- //
	function __construct() {
		$this->user = ODBC_TIBUPLAST_USER;
		$this->pass = ODBC_TIBUPLAST_PASS;			
		$this->source = ODBC_TIBUPLAST_SOURCE;
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
		) or die ("Kan niet inloggen, controleer credentials<br>Source: " . $this->source . "<br>User: " . $this->user . "<br>Pass: " . $this->pass );
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