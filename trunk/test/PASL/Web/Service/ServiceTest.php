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

if(!defined('SRCPATH'))
{
	define('SRCPATH', realpath(dirname(__FILE__).'/../../../../src/PASL'));
	ini_set('include_path', get_include_path().PATH_SEPARATOR . SRCPATH);
}

require_once('simpletest/autorun.php');
require_once('PASL/Web/Service/Gateway.php');

class PASL_Web_ServiceTest extends UnitTestCase
{
	/**
	 * @var PASL_Web_Service
	 */
	private $service;

	/**
	 * @var PASL_Web_Service_Gateway
	 */
	private $gateway;

	public function PASL_Web_ServiceTest()
	{
		$this->UnitTestCase("PASL Web Service Tests");
	}

	public function TestServiceInstantiation()
	{
		// Stubbed test to keep from the testsuite from breaking
		$this->gateway = \PASL\Web\Service\Gateway::GetInstance();
		$this->service = $this->gateway->service;

		$this->assertIsA($this->service, 'PASL\Web\Service');

		// NOT REALLY SURE HOW TO WRITE A TEST HERE WHEN IT REQUIRES A REAL POST/GET/PUT/DELETE REQUEST
	}

	public function TestRestService()
	{
		// Since we're not coming from a web server we need to set the mode
//		$_SERVER['REQUEST_METHOD'] = 'GET';
//		$_SERVER['REQUEST_URI'] = '/rest/SampleService/ServiceMethod/Argument';
//
//		$this->service->setHandler($this->service);
//
//		$this->service->handle();
//		$response = $this->service->getResponder()->getResponse();
//		$this->assertEqual($response, 'ServiceMethod');
//
//		$this->service->getResponder()->clearResponseBuffer();
//
//		$_GET['method'] = 'ServiceMethod2';
//		$this->service->handle();
//		$response = $this->service->getResponder()->getResponse();
//		$this->assertEqual($response, 'ServiceMethod2');
	}
}

?>