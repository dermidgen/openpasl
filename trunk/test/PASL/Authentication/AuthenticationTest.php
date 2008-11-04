<?php

if(!defined('SRCPATH'))
{
	define('SRCPATH', realpath(dirname(__FILE__).'/../../../src/PASL'));
	ini_set('include_path', get_include_path().PATH_SEPARATOR . SRCPATH);
}

require_once('simpletest/autorun.php');
require_once('DB/DB.php');
require_once('Authentication/Authentication.php');

class PASL_AuthenticationTest extends UnitTestCase
{
	public $strMyDsn;
	public $aMyDsn = Array();

	public $aPostCredentials = Array();
	public $aPostCredentialsMD5 = Array();

	function PASL_AuthenticationTest()
	{
		$this->UnitTestCase("PASL Authetication Tests");

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

		$this->aPostCredentials['username'] = 'openpasl_test';
		$this->aPostCredentials['password'] = 'openpasl_test';

		$this->aPostCredentialsMD5['username'] = 'openpasl_test_md5';
		$this->aPostCredentialsMD5['password'] = 'openpasl_test';
	}

	function TestProvider_mysql()
	{
		$db = PASL_DB::singleton($this->strMyDsn);

		$oAuth = new PASL_Authentication('mysql');
		$authProvider = $oAuth->getProvider();

		$this->assertIsA($authProvider, "PASL_Authentication_iProvider");
		$this->assertIsA($authProvider, "PASL_Authentication_Provider_mysql");
	}

	function TestAuth_mysql()
	{
		$db = PASL_DB::singleton($this->strMyDsn);

		$oAuth = new PASL_Authentication('mysql');

		/**
		 * @var PASL_Authentication_Provider_mysql
		 */
		$authProvider = $oAuth->getProvider();
		$authProvider->setDriver($db);
		$authProvider->setQuery('SELECT * FROM `pasl_authentication_tests` WHERE `username`="%s" LIMIT 1');

		// Set encryption to none
		$authProvider->setEncryption('none');

		$this->assertTrue($oAuth->authenticate($this->aPostCredentials));
	}

	function TestAuth_mysql_md5()
	{
		$db = PASL_DB::singleton($this->strMyDsn);

		$oAuth = new PASL_Authentication('mysql');

		/**
		 * @var PASL_Authentication_Provider_mysql
		 */
		$authProvider = $oAuth->getProvider();
		$authProvider->setDriver($db);
		$authProvider->setQuery('SELECT * FROM `pasl_authentication_tests` WHERE `username`="%s" LIMIT 1');

		$this->assertTrue($oAuth->authenticate($this->aPostCredentialsMD5));
	}
}
?>