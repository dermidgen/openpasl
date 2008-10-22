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
 * Authentication provides basic authentication mechanisms through various drivers.
 * Each driver parses a formatted DSN providing both username and password keys.
 * Additional keys may be supplied for additional granularity in the authentication
 * process.
 *
 * @package PASL
 * @subpackage PASL_Authentication
 * @category Authentication
 * @author Danny Graham <good.midget@gmail.com>
 */
class PASL_Authentication
{
	/**
	 * @var PASL_Authentication_iDriver
	 */
	private $driver = null;

	public function __construct($dsn)
	{
		if (!is_null($dsn)) $this->setDSN($dsn);
	}

	private function validateDSN($aDsn)
	{

	}

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
	 * @author Danny Graham <good.midget@gmail.com>
	 */
	private function parseDSN($dsn)
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
	 * Factory for instantiating the specified driver
	 *
	 * @param string Name of the driver to instantiate
	 * @return PASL_Authentication_iDriver
	 */
	private function driverFactory($strDriver)
	{
		switch($strDriver)
		{
			case "saml":
				$driver = null;
			break;
			default:
				$driver = null;
			break;
		}

		return $driver;
	}

	/**
	 * Sets an option in the driver
	 *
	 * @see PASL_Authentication_iDriver::setOption()
	 *
	 * @param string $key The option value to be set
	 * @param string $value The value to pass
	 * @return bool
	 */
	public function setOption($key, $value)
	{
		return $this->driver->setOption($key, $value);
	}

	/**
	 * Gets an option from the driver
	 *
	 * @see PASL_Authentication_iDriver::getOption()
	 *
	 * @param string $key The option value to be returned
	 * @return mixed
	 */
	public function getOption($key)
	{
		return $this->driver->getOption($key);
	}

	/**
	 * Sets the data source name for the authentication routines.
	 * This allows the specification of a driver, key indexes for
	 * validating user credentials against, a credential parser,
	 * or a database dsn.
	 *
	 * @see parseDSN()
	 *
	 * @param String|Array $dsn The dsn for authentication
	 * @return bool
	 */
	public function setDSN($dsn)
	{
		$aDsn = (get_type($dsn) == "string") ? $this->parseDSN($dsn) : $dsn; // Array
		if (!$this->validateDSN($aDsn)) return false;

		$this->driver = $this->driverFactory($aDsn['driver']);
		return true;
	}

	/**
	 * Returns the current authentication driver
	 *
	 * @return PASL_Authentication_iDriver
	 */
	public function getDriver()
	{
		return $this->driver;
	}

	/**
	 * Authenticate a user
	 *
	 * @param mixed $credentials
	 *
	 * @return bool
	 */
	public function authenticate($credentials)
	{
		return $this->driver->authenticate($credentials);
	}
}
?>