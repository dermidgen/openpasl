<?php

if(!defined('SRCPATH')) define('SRCPATH', '../../../src/PASL/');

require_once('simpletest/autorun.php');
require_once(SRCPATH . 'DB/DB.php');

class PASL_DBTest extends UnitTestCase
{
	/**
	 * @var String
	 */
	public $strMyDsn;

	/**
	 * @var Array
	 */
	public $aMyDsn = Array();

	public function __construct()
	{
		$this->strMyDsn = "mysql://username:password@hostname.com/default_schema";

		$this->aMyDsn["phptype"] = "mysql";
		$this->aMyDsn["hostspec"] = "hostname.com";
		$this->aMyDsn["database"] = "default_schema";
		$this->aMyDsn["dsn"] = $this->strMyDsn;
		$this->aMyDsn["username"] = "username";
		$this->aMyDsn["password"] = "password";
		$this->aMyDsn["dbsyntax"] = '';
		$this->aMyDsn["protocol"] = 'tcp';
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
		/* !!! CANNOT RUN THIS TEST WITHOUT POINTING AT A LIVE DSN !!! */
		$strLiveDsn = "mysql://openpasl_test:openpasl_test@localhost/openpasl_test";

		// Test new instance factory for a portable MDB2 driver
		$dbDriver = PASL_DB::factory($strLiveDsn, false, true);
		$this->assertIsA($dbDriver, 'MDB2_Driver_common');
		$this->assertIsA($dbDriver, 'MDB2_Driver_mysql');

		// Test new instance factory for a portable MDB2 driver
		$dbDriver = PASL_DB::singleton($strLiveDsn, false, true);
		$this->assertIsA($dbDriver, 'MDB2_Driver_common');
		$this->assertIsA($dbDriver, 'MDB2_Driver_mysql');
	}
}

?>