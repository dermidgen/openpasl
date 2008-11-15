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
 * Provides a base class for publishing web services. Can be used through
 * direct instantiation or by extension.
 *
 * @package PASL_Web
 * @subpackage PASL_Web_Service
 * @category Web
 * @author Danny Graham <good.midget@gmail.com>
 */

class PASL_Web_Service
{
	/**
	 * String name of the type of service we've said we want to run.
	 * (SOAP, REST, AMF, etc.)
	 *
	 * @var string
	 */
	public $serviceType = null;

	/**
	 * Instance of the service provider used to handle incoming service requests
	 *
	 * @var PASL_Web_Service_iServiceProvider
	 */
	protected $provider = null;

	/**
	 * Instance of the service responder used to package service responses
	 *
	 * @var PASL_Web_Service_iServiceResponder
	 */
	protected $responder = null;

	/**
	 * Data to be sent as reponse to the service request
	 *
	 * @var mixed
	 */
	public $responsePayload = null;


	/**
	 * The object scope to try for implemented methods.
	 * When the handler attempts to call service methods this
	 * sets the object scope where the service methods should exist.
	 *
	 * @var object
	 */
	protected $oHandlerContext = null;

	/**
	 * Factory for instantiating service providers
	 *
	 * @param string The name of the provider mode
	 * @return PASL_Web_Service_iServiceProvider
	 */
	private function providerFactory($strModeType)
	{
		$className = 'PASL_Web_Service_Provider_' . $strModeType;

		if(!class_exists($className, false))
		{
			$dPath = dirname(__FILE__)."/Provider/{$strModeType}.php";

			if (!file_exists($dPath)) throw new Exception("Class not found at path {$dPath}");
			require_once($dPath);
		}

		$provider = new $className();

		if (!($provider instanceof PASL_Web_Service_iServiceProvider))
			throw new Exception("Provider does not implement iServiceProvider");

		return $provider;
	}

	/**
	 * Factory for instantiating service responders
	 *
	 * @param string The name of the responder mode
	 * @return PASL_Web_Service_iServiceResponder
	 */
	private function responderFactory($strModeType)
	{
		$className = 'PASL_Web_Service_Responder_' . $strModeType;

		if(!class_exists($className, false))
		{
			$dPath = dirname(__FILE__)."/Responder/{$strModeType}.php";

			if (!file_exists($dPath)) throw new Exception("Class not found at path {$dPath}");
			require_once($dPath);
		}

		$responder = new $className();

		if (!($responder instanceof PASL_Web_Service_iServiceResponder))
			throw new Exception("Responder does not implement iServiceResponder");

		return $responder;
	}

	/**
	 * Calls the appropriate method within the scope of the handler
	 * and returns the result.
	 *
	 * @param object Object to provide scope for calling the handler method
	 * @param PASL_Web_Service_Request The full request object
	 *
	 * @return mixed
	 */
	protected function callHandler($oHandler, $oRequest)
	{
		if (!method_exists($oHandler, $oRequest->method)) throw new Exception('Method not implemented');

		$response = $oHandler->{$oRequest->method}($oRequest);

		return $response;
	}

	/**
	 * Returns the instance of the current provider
	 *
	 * @return PASL_Web_Service_iServiceProvider
	 */
	public function getProvider()
	{
		return $this->provider;
	}

	/**
	 * Returns the instance of the current responder
	 *
	 * @return PASL_Web_Service_iServiceResponder
	 */
	public function getResponder()
	{
		return $this->responder;
	}

	/**
	 * Set the scope object for running the handler method
	 *
	 * @param object The object containing the handler method
	 */
	public function setHandler($oHandler=null)
	{
		// TODO: Implement factory for instantiating handler objects
		$this->oHandlerContext = $oHandler;
	}

	/**
	 * Sets the provider and responder instances based on
	 * the provided service mode
	 *
	 * @param String Service mode
	 * + "REST"
	 * + "SOAP"
	 * + "AMF"
	 *
	 * @return void
	 */
	public function setServiceMode($strModeType)
	{
		$this->serviceType = $strModeType;
		$this->provider = $this->providerFactory($strModeType);
		$this->responder = $this->responderFactory($strModeType);
	}

	public function handle()
	{
		$oRequest = $this->provider->parseRequest();

		// TODO: Inspect the request object for handler context

		if ($this->oHandlerContext == null) // We'll try local scope
			$this->responder->addPayload($this->callHandler($this,$oRequest));
		else // We'll use the handler object
			$this->responder->addPayload($this->callHandler($this->oHandlerContext, $oRequest));
	}

	public function send()
	{
		$strResponse = $this->responder->sendResponse();
	}
}

?>