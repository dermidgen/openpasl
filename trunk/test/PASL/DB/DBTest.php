<?php

if(!defined('SRCPATH'))
{
	define('SRCPATH', realpath(dirname(__FILE__).'/../../../src/PASL'));
	ini_set('include_path', get_include_path().PATH_SEPARATOR . SRCPATH);
}

require_once('simpletest/autorun.php');
require_once('DB/DB.php');

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
		/* !!! CANNOT RUN THIS TEST WITHOUT POINTING AT A LIVE DSN !!! */
		$this->strMyDsn = "mysql://openpasl_test:openpasl_test@localhost/openpasl_test";

		$this->aMyDsn["phptype"] = "mysql";
		$this->aMyDsn["hostspec"] = "localhost";
		$this->aMyDsn["database"] = "openpasl_test";
		$this->aMyDsn["dsn"] = $this->strMyDsn;
		$this->aMyDsn["username"] = "openpasl_test";
		$this->aMyDsn["password"] = "openpasl_test";
		$this->aMyDsn["dbsyntax"] = '';
		$this->aMyDsn["protocol"] = 'tcp';
	}

	/**
	 * @param PASL_DB_Driver_Common|MDB2_Driver_common $dbObject
	 */
	private function testBasicQueryMethods($dbObject)
	{
		$sql = "SELECT * FROM pasl_query_tests";

		$expectedRecord = Array();
		$expectedRecord['id'] = "1";
		$expectedRecord['name'] = 'db_test';
		$expectedRecord['sequence'] = 'primary';
		$expectedRecord['timestamp'] = '0000-00-00 00:00:00';

		$result = $dbObject->queryCol($sql, 'name');
		$this->assertEqual($result[0], $expectedRecord['name']);

		$result = $dbObject->queryCol($sql, 1);
		$this->assertEqual($result[0], $expectedRecord['name']);

		$result = $dbObject->queryOne($sql, 'name');
		$this->assertEqual($result, $expectedRecord['name']);

		$result = $dbObject->queryOne($sql, 1);
		$this->assertEqual($result, $expectedRecord['name']);

		$result = $dbObject->queryRow($sql);
		$this->assertIsA($result, 'Array');
		$this->assertIdentical($result, $expectedRecord);

		$result = $dbObject->queryAll($sql);
		$this->assertIsA($result, 'Array');
		$this->assertIdentical($result[0], $expectedRecord);
	}

	private function testPASLMySQL()
	{
		// Test new instance factory for a native/custom driver
		$dbDriver = PASL_DB::factory($this->strMyDsn, false, false);
		$this->assertIsA($dbDriver, 'PASL_DB_Driver_Common');
		$this->assertIsA($dbDriver, 'PASL_DB_Driver_MySQL');

		// Test singleton factory for a native/custom driver
		$dbDriver = PASL_DB::singleton($this->strMyDsn, false, false);
		$this->assertIsA($dbDriver, 'PASL_DB_Driver_Common');
		$this->assertIsA($dbDriver, 'PASL_DB_Driver_MySQL');

		$sql = "SELECT * FROM pasl_query_tests";

		// Test the basic query response type
		$result = $dbDriver->query($sql);
		$this->assertIsA($result, 'resource');

		// Test basic query methods (should be compatible with MDB2 api)
		$this->testBasicQueryMethods($dbDriver);
	}

	private function testMDB2Instantiation()
	{
		// Test new instance factory for a portable MDB2 driver
		$dbDriver = PASL_DB::factory($this->strMyDsn, false, true);
		$this->assertIsA($dbDriver, 'MDB2_Driver_common');
		$this->assertIsA($dbDriver, 'MDB2_Driver_mysql');

		// Test new instance factory for a portable MDB2 driver
		$dbDriver = PASL_DB::singleton($this->strMyDsn, false, true);
		$this->assertIsA($dbDriver, 'MDB2_Driver_common');
		$this->assertIsA($dbDriver, 'MDB2_Driver_mysql');
	}

	public function testDSNParsing()
	{
		// Test DSN Parsing
		$DBDsn = PASL_DB::ParseDSN($this->strMyDsn);
		$this->assertIsA($DBDsn, 'Array', 'PASL_DB::ParseDSN should return an array');
		$this->assertIdentical($this->aMyDsn,$DBDsn,"PASL_DB::ParseDSN did not return the expected value - perhaps it's not working right");
	}

	public function testMDB2Drivers()
	{
		$this->testMDB2Instantiation();
	}

	public function testPASLDrivers()
	{
		$this->testPASLMySQL();
	}
}

?>