<?php
/**
 * OpenPASL
 *
 * Copyright (c) 2008, Danny Graham, Scott Thundercloud
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *   * Neither the name of the Danny Graham, Scott Thundercloud, nor the names of
 *     their contributors may be used to endorse or promote products derived from
 *     this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @copyright Copyright (c) 2008, Danny Graham, Scott Thundercloud
 */

/**
 * DB is more of a utility than an abstraction layer.  DB is designed to
 * allow the implementation of some basic concepts from PEAR::MDB2 using
 * PHP's raw driver support.  While some of the features of MDB2 are
 * useful, the overhead is totally unecessary.  However, in the event
 * that you need to write portable queries to a datasource that may be
 * subject to change, DB provides access to MDB2.  Use of raw drivers is
 * encouraged at the expense of writing totally portable queries.  In an
 * effort to reduce refactoring, should certain aspects of application
 * require switching from a raw driver to MDB2, we try to match some of
 * the common API calls from MDB2.  This allows a simple switch of the
 * DB driver instance to an MDB2 instance through DB::portable().

 * @package PASL
 * @subpackage PASL_DB
 * @category Database
 * @static
 * @author Danny Graham <good.midget@gmail.com>
 */
class PASL_DB
{
	/**
	 * Database connector
	 * @access public
	 * @static
	 * @var MDB2_Driver_mysql
	 */
	public static $mdb2 = null;

	/**
	 * An array of instantiated raw drivers
	 *
	 * @access private
	 * @static
	 * @var array
	 */
	private static $nativeDrivers = Array();

	/**
	 * An array of instantiated MDB2 drivers
	 *
	 * @access private
	 * @static
	 * @var array
	 */
	private static $MDB2Drivers = Array();


	/**
	 * Function for parsing DSN strings.
	 *
	 * @param String DSN string supporting:
	 *  + phptype://username:password@protocol+hostspec:110//usr/db_file.db?mode=0644
	 *  + phptype://username:password@hostspec/database_name
	 *  + phptype://username:password@hostspec
	 *  + phptype://username@hostspec
	 *  + phptype://hostspec/database
	 *  + phptype://hostspec
	 *  + phptype(dbsyntax)
	 *  + phptype
	 *
	 * @return Array An associative array with the keys:
	 *  + phptype:  Database backend used in PHP (mysql, odbc etc.)
	 *  + dbsyntax: Database used with regards to SQL syntax etc.
	 *  + protocol: Communication protocol to use (tcp, unix etc.)
	 *  + hostspec: Host specification (hostname[:port])
	 *  + database: Database to use on the DBMS server
	 *  + username: User name for login
	 *  + password: Password for login
	 *
	 * @author Scott Thundercloud <scott.tc@gmail.com>
	 * @see MDB2::parseDSN
	 */
	public static function ParseDSN($DSN)
	{
		$Matches = Array();

		if(!preg_match("/(^[a-zA-Z]*)\:[\/|\\\]{2}(.*)\:?(.*)\@?(.*)\/(.*)/i", $DSN, $Matches)) return false;

		// If username and password or a username exists.
		if(preg_match("/:|@/i", $Matches[2]))
		{
			$Username = '';
			$Password = '';
			$Host = '';

			$SplitUserPass = preg_split("/:|@/", $Matches[2]);

			$Username = $SplitUserPass[0];

			if(count($SplitUserPass) == 2) $Host = $SplitUserPass[1];
			elseif(count($SplitUserPass) == 3)
			{
				$Password = $SplitUserPass[1];
				$Host = $SplitUserPass[2];
			}
		}

		$Array = Array();
		$Array["phptype"] = $Matches[1];
		$Array["hostspec"] = ($Host) ? $Host : $Matches[2];
		$Array["database"] = $Matches[5];
		$Array["dsn"] = $Matches[0];
		$Array["username"] = $Username;
		$Array["password"] = $Password;
		$Array["dbsyntax"] = '';
		$Array["protocol"] = 'tcp';

		return $Array;
	}

	/**
	 * Get an instance of native/custom driver from PASL
	 *
	 * @param Array $dsn
	 * @param Bool $singleton
	 * @return PASL_DB_Driver_mysql
	 */
	private static function PASL_Factory($dsn, $singleton=false)
	{
		$driver = $dsn['phptype'];
		$dbUsername = $dsn['username'];
		$dbPassword = $dsn['password'];
		$dbHostSpec = $dsn['hostspec'];
		$dbDatabase = $dsn['database'];

		$className = "PASL_DB_Driver_" . $driver;

		if(!class_exists($className, false))
		{
			$dPath = dirname(__FILE__)."/Driver/{$driver}.php";

			if (!file_exists($dPath)) throw new Exception("Driver not found at path {$dPath}");
			require_once($dPath);
		}

		if(!$singleton) return new $className($dbHostSpec, $dbUsername, $dbPassword, $dbDatabase);
		else
		{
			$ReflectedClass = new ReflectionClass($className);

			$Host = $ReflectedClass->getProperty("host");
			$Username = $ReflectedClass->getProperty("username");
			$Password = $ReflectedClass->getProperty("password");
			$Database = $ReflectedClass->getProperty("database");

			$Host->setValue(NULL, $dbHostSpec);
			$Username->setValue(NULL, $dbUsername);
			$Password->setValue(NULL, $dbPassword);
			$Database->setValue(NULL, $dbDatabase);

			$GetInstance = $ReflectedClass->getMethod("GetInstance");
			return $GetInstance->invoke(NULL);
		}

		if (!$db) return null;
		return $db;
	}

	/**
	 * Get a fresh instance of DB Connection Driver
	 *
	 * @param String|Array $dsn
	 * @param mixed $options
	 * @param Bool $portable
	 * @return MDB2_Driver_mysql|PASL_DB_Driver_mysql
	 */
	public static function factory($dsn,$options=false,$portable=false)
	{
		// Ensure that the DSN is in Array format
		if (!is_array($dsn)) $dsn = PASL_DB::ParseDSN($dsn);

		if ($portable) // Kick out MDB2 Driver
		{
			require_once("MDB2.php");
			$db = MDB2::factory($dsn, $options);
		}
		else // We'll go with a native/custom driver
		{
			$db = PASL_DB::PASL_Factory($dsn, false);
		}

		return $db;
	}

	/**
	 * Get a singleton instance of DB Connection Driver
	 *
	 * @param String|Array $dsn
	 * @param mixed $options
	 * @param Bool $portable
	 * @return MDB2_Driver_mysql|PASL_DB_Driver_mysql
	 */
	public static function singleton($dsn,$options=false,$portable=false)
	{
		// Ensure that the DSN is in Array format
		if (!is_array($dsn)) $dsn = PASL_DB::ParseDSN($dsn);

		$driverIndex = $dsn['phptype'] . '_' . $dsn['hostspec'];

		if ($portable) // Kick out MDB2 Driver
		{
			require_once("MDB2.php");

			// Check the existing driver stack and return one if it already exists
			if (isset(PASL_DB::$MDB2Drivers[$driverIndex])) return PASL_DB::$MDB2Drivers[$driverIndex];

			$db = MDB2::singleton($dsn, $options);
			PASL_DB::$MDB2Drivers[$driverIndex] = $db; // Stick the driver in our local stack
		}
		else // We'll go with a native/custom driver
		{

			// Check the existing driver stack and return one if it already exists
			if (isset(PASL_DB::$nativeDrivers[$driverIndex])) return PASL_DB::$nativeDrivers[$driverIndex];

			$db = PASL_DB::PASL_Factory($dsn, true);
			PASL_DB::$nativeDrivers[$driverIndex] = $db; // Stick the driver in our local stack
		}

		return $db;
	}

	/**
	 * Returns an existing driver connection
	 *
	 * @param String $instanceName should be formatted as phptype_host
	 * @param bool $portable return a driver from the MDB2Driver stack
	 * @return PASL_DB_Driver_Driver_Common
	 */
	public static function getInstance($instanceName, $portable=false)
	{
		if ($portable)
		{
			if (isset(PASL_DB::$MDB2Drivers[$instanceName])) return PASL_DB::$MDB2Drivers[$instanceName];
		}
		else
		{
			if (isset(PASL_DB::$nativeDrivers[$instanceName])) return PASL_DB::$nativeDrivers[$instanceName];
		}

		return null;
	}
}

?>