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

require_once('PASL/Web/Service/iServiceProvider.php');
require_once('PASL/Web/Service/amfphp/globals.php');
require_once('PASL/Web/Service/amfphp/core/amf/app/Gateway.php');

/**
 * Provides AMF based service support through AMFPHP. We do a little
 * cleanup here to ensure that the classpath is correctly set for AMFPHP
 * to find, instantiate and call the handler.
 *
 * @package PASL_Web_Service
 * @subpackage PASL_Web_Service_Provider
 * @category Web
 * @author Danny Graham <good.midget@gmail.com>
 */
class PASL_Web_Service_Provider_AMF implements PASL_Web_Service_iServiceProvider
{
	private $productionServer = false;

	/**
	 * The AMFPHP Gateway for handling AMF service requests
	 *
	 * @var Gateway
	 */
	public $gateway = null;

	public function __construct()
	{
		$this->gateway = new Gateway();
		$this->gateway->setCharsetHandler("utf8_decode", "ISO-8859-1", "ISO-8859-1");

		//Error types that will be rooted to the NetConnection debugger
		$this->gateway->setErrorHandling(E_ALL ^ E_NOTICE);
		$this->gateway->enableGzipCompression(25*1024);
	}

	/**
	 * Set's AMFPHP in either debug or production mode.
	 * When used with no parameter a boolean value will be returned
	 * indicating the whether production mode is on or off
	 *
	 * @param bool $bVal
	 * @return bool
	 */
	public function isProductionMode($bVal=null)
	{
		if (!is_null($bVal) && is_bool($bVal))
		{
			$this->productionServer = $bVal;

			if ($this->productionServer)
			{
				// Disable debugging, remote tracing, and service browser
				$this->gateway->disableDebug();
				// Keep the Flash/Flex IDE player from connecting to the gateway. Used for security to stop remote connections.
				$this->gateway->disableStandalonePlayer();
			}
		}

		return $this->productionServer;
	}

	/**
	 * Parse the request / request object
	 *
	 * @param PASL_Web_Service_Request The intial request object
	 */
	public function parseRequest($oRequest)
	{
		$this->gateway->setClassPath($oRequest->operationClassPath);
		return $oRequest;
	}

	public function handle()
	{
		$this->gateway->service();
	}
}

?>