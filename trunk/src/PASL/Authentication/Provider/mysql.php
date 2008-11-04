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

require_once('PASL/DB/DB.php');
require_once('PASL/Authentication/iProvider.php');
require_once('PASL/Authentication/Provider/common.php');

/**
 * Provides a mysql based authentication provider. This provider is designed to
 * allow complete control over authentication logic by simply changing the base
 * query used to gather user information.  Also supports several types of
 * encrypted password storage (currently on MD5).
 *
 * @package PASL_Authentication
 * @subpackage PASL_Authentication_Provider
 * @category Authentication
 * @author Danny Graham <good.midget@gmail.com>
 */
class PASL_Authentication_Provider_mysql extends PASL_Authentication_Provider_common implements PASL_Authentication_iProvider
{
	private $encryptedPasswords = true;
	private $encryptionMethod = 'md5';
	private $authQuery = 'SELECT * FROM `users` WHERE `username`="%s" LIMIT 1';

	/**
	 * @var PASL_DB_Driver_mysql
	 */
	private $driver = null;

	public function __construct()
	{

	}

	private function validatePassword($userPassword, $cmpPassword)
	{
		if ($this->encryptedPasswords)
		{
			switch($this->encryptionMethod)
			{
				case 'md5':
					$bMatch = (md5($userPassword) == $cmpPassword);
				break;
				default: // Default is md5 encryption
					$bMatch = (md5($userPassword) == $cmpPassword);
			}
		}
		else $bMatch = ($userPassword == $cmpPassword);

		if ($bMatch) return true;
		else
		{
			$this->errors[] = PASL_AUTH_BAD_PASSWORD;
			return false;
		}
	}

	public function setEncryption($encryptionType)
	{
		if ($encryptionType == 'none') $this->encryptedPasswords = false;
		else
		{
			$this->encryptedPasswords = true;
			$this->encryptionMethod = $encryptionType;
		}
	}

	public function setDSN($dsn)
	{
		if (!$this->driver) $this->driver = PASL_DB::singleton($dsn);
	}

	public function setDriver(PASL_DB_Driver_mysql $oDriver)
	{
		$this->driver = $oDriver;
	}

	public function setQuery($strQuery)
	{
		$this->authQuery = $strQuery;
	}

	public function authenticate($credentials)
	{
		// TODO: Implement data validation/cleaning against supplied credentials

		$query = sprintf($this->authQuery, $credentials['username']);
		$res = $this->driver->queryRow($query);

		if (!$res)
		{
			$this->errors[] = PASL_AUTH_BAD_USER;
			return false;
		}

		return $this->validatePassword($credentials['password'], $res['password']);
	}
}
?>