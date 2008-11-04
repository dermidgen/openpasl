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
	 * @var PASL_Authentication_iProvider
	 */
	private $provider = null;

	public function __construct($strProvider)
	{
		$this->setProvider($strProvider);
	}

	private function parseCredentials($credentials)
	{
		return $credentials;
	}

	/**
	 * Factory for instantiating the specified auth provider
	 *
	 * @param string Name of the provider to instantiate
	 * @return PASL_Authentication_iProvider
	 */
	private function providerFactory($strProvider)
	{
		switch($strProvider)
		{
			case "saml":
				$provider = null;
			break;
			case "mysql":
				require_once('PASL/Authentication/Provider/mysql.php');
				$provider = new PASL_Authentication_Provider_mysql();
			break;
			default:
				$provider = null;
			break;
		}

		return $provider;
	}

	/**
	 * Sets the authentication provider to be used
	 *
	 * @param string Name of the provider to use
	 * @return void
	 */
	public function setProvider($strProvider)
	{
		$this->provider = $this->providerFactory($strProvider);
	}

	/**
	 * Returns the current instantiated auth provider.
	 * + This can be useful for setting provider specific options.
	 * + For type hinting use phpdoc to declare the actual provider type.
	 *
	 * @return PASL_Authentication_iProvider
	 */
	public function getProvider()
	{
		return $this->provider;
	}

	public function getError()
	{
		return $this->provider->getError();
	}

	public function authenticate($credentials)
	{
		$payload = $this->parseCredentials($credentials);

		$authed = (bool) $this->provider->authenticate($payload);
		return $authed;
	}
}
?>