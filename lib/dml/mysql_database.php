<?php

class mysql_database{
	
	protected $host;
	protected $port;
	protected $user;
	protected $pass;

	function __construct($Host, $Port, $User, $Pass){
		$this->host     = $Host;
        $this->dbname   = $DbName;
        $this->username = $UserName;
        $this->password = $Password;
	}

}