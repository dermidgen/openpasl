<?php

if(!defined('SRCPATH')) define('SRCPATH', '../../../src/PASL/');

require_once('simpletest/autorun.php');
require_once(SRCPATH . 'DB/DB.php');

class PASL_DBTest extends UnitTestCase
{
	public $strMyDsn;
	public $aMyDsn = Array();

	public function __construct()
	{
		$this->strMyDsn = "mysql://username:password@hostname.com/default_schema";

		$this->aMyDsn["DBType"] = "mysql";
		$this->aMyDsn["Host"] = "hostname.com";
		$this->aMyDsn["Database"] = "default_schema";
		$this->aMyDsn["DSN"] = $this->strMyDsn;
		$this->aMyDsn["Username"] = "username";
		$this->aMyDsn["Password"] = "password";
	}

	function testDSNParsing()
	{
		// Test DSN Parsing
		$DBDsn = PASL_DB::ParseDSN($this->strMyDsn);
		$this->assertIsA($DBDsn, 'Array', 'PASL_DB::ParseDSN should return an array');
		$this->assertIdentical($this->aMyDsn,$DBDsn,"PASL_DB::ParseDSN did not return the expected value - perhaps it's not working right");
	}

	function testPASLDrivers()
	{
		// Test new instance factory for a native/custom driver
		$dbDriver = PASL_DB::factory($this->strMyDsn, false, false);
		$this->assertIsA($dbDriver, 'PASL_DB_Driver_Common');
		$this->assertIsA($dbDriver, 'PASL_DB_Driver_MySQL');

		// Test singleton factory for a native/custom driver
		$dbDriver = PASL_DB::singleton($this->strMyDsn, false, false);
		$this->assertIsA($dbDriver, 'PASL_DB_Driver_Common');
		$this->assertIsA($dbDriver, 'PASL_DB_Driver_MySQL');
	}

	function testMDB2Drivers()
	{
		// Test new instance factory for a portable MDB2 driver
		/* !!! CANNOT RUN THIS TEST WITHOUT POINTING AT A LIVE DSN !!! */
//		$dbDriver = PASL_DB::factory($dsn, false, true);
//		$this->assertIsA($dbDriver, 'MDB2_Driver_common');
//		$this->assertIsA($dbDriver, 'MDB2_Driver_mysql');
	}
}

?>