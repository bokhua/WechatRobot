<?php

require_once(dirname(__FILE__) . '/lib.php');

global $CFG, $DB;

$dbclassname = $CFG->dbtype.'_database';

require_once($CFG->dirroot.'/lib/dml/'.$dbclassname.'.php');

$DB = new $dbclassname($CFG->dbhost, $CFG->dbport, $CFG->dbuser, $CFG->dbpass);

