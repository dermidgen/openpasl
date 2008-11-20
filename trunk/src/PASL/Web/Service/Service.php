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

require_once('PASL/Web/Service/Request.php');


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
	protected $oHandler = null;

	public $sBaseClassBath = null;
	public $sClassPath = null;

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
	 * Factory for instantiating a handler class
	 *
	 * @param string The name of the class to handle the service request
	 * @return object
	 */
	private function handlerFactory($strClassName)
	{
		// Compatability for calling packaged classes with dot notation
		// e.g. SamplePackage.SampleSubPackage.SampleService
		$pathParts = explode('.', $strClassName);

		$strClassName = array_pop($pathParts);
		$classPath = join('/',$pathParts);
		$classPath = ($classPath != '') ? $this->sBaseClassPath . '/' . $classPath . '/' : $this->sBaseClassPath . '/';
		$classPath = $classPath . $strClassName . '.php';
		if (!file_exists($classPath)) throw new Exception('Class ' . $strClassName . ' not found at: ' . $classPath);

		require_once($classPath);

		return new $strClassName();
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

		$response = call_user_func_array(array($oHandler, $oRequest->method), $oRequest->methodArgs);

		return $response;
	}

	/**
	 * Parses the incoming request data to determine which type of service
	 * the request is addressing, and generate a request object based
	 * on how the underlying provider parses the request data.
	 *
	 * @return PASL_Web_Service_Request
	 */
	protected function parseRequest()
	{
		$requestMethod = $_SERVER['REQUEST_METHOD'];
		$requestURI = $_SERVER['REQUEST_URI'];

		$oRequest = new PASL_Web_Service_Request();

		/**
		 * A broken out array of the request
		 *
		 * Requests should be path based with some variation between service types
		 * + REST:
		 * +   [domainroot]/{service identifier}/{classname}/{method}/{params}
		 * +   http://service.company.com/rest/mymodule/mymethod/myparam1/myparam2/myparam3
		 * + SOAP:
		 * +   [domainroot]/{service identifier}
		 * +   http://service.company.com/soap
		 * + AMF:
		 * +   [domainroot]/{service identifier}/{classpath}
		 * +   http://service.company.com/amf/modulepath
		 */
		$oRequestParts = explode('/',$requestURI);
		if ($oRequestParts[0] == '') array_shift($oRequestParts);

		$oRequest->serviceType = $oRequestParts[0];

		$oRequest->requestURI = $requestURI;
		$oRequest->oRequestHash = $oRequestParts;

		// For AMF based services AMFPHP will handle all this from the request payload
		if ($oRequest->serviceType != 'AMF')
		{
			// Set the object scope to handle the service request
			$oRequest->operationClass = ($oRequest->serviceType == 'REST' || $oRequest->serviceType == 'AMF') ? $oRequestParts[1] : null;
			if (is_null($this->oHandler)) $this->setHandler($this->handlerFactory($oRequest->operationClass));
		}

		// Set the class path for AMF publishing
		$oRequest->operationClassPath = $this->sBaseClassPath;

		$this->setServiceMode($oRequest->serviceType);
		return $this->provider->parseRequest($oRequest);
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
	 * @return void
	 */
	public function setHandler($oHandler)
	{
		$this->oHandler = $oHandler;

		// We need to get some filesystem information on the object for setting up AMF service handlers
		// AMFPHP requires us to provide a classpath to provision services.
		$className = get_class(($oHandler == null) ? $this : $oHandler);

		$reflected = new ReflectionClass($className);
		$this->sClassPath = dirname($reflected->getFileName());
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
		$strModeType = strtoupper($strModeType);

		$this->serviceType = $strModeType;
		$this->provider = $this->providerFactory($strModeType);
		$this->responder = $this->responderFactory($strModeType);
	}

	public function handle()
	{
		$oRequest = $this->parseRequest();

		// If it's an AMF service request we'll just let AMFPHP handle the whole thing
		if ($this->serviceType == 'AMF') return $this->provider->handle();

		// Inspect the request object for handler context
		if ($this->oHandler == null) // We'll try local scope
			$this->responder->addPayload($this->callHandler($this,$oRequest));
		else // We'll use the handler object
			$this->responder->addPayload($this->callHandler($this->oHandler, $oRequest));

		$this->send();
	}

	public function send()
	{
		print $this->responder->getResponse();
	}
}

?>