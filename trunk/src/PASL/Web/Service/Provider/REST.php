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
require_once('PASL/Web/Service/Request.php');

/**
 * Provider for REST based services.
 *
 * @package PASL_Web_Service
 * @subpackage PASL_Web_Service_Provider
 * @category Web
 * @author Danny Graham <good.midget@gmail.com>
 */
class PASL_Web_Service_Provider_Rest implements PASL_Web_Service_iServiceProvider
{
	/**
	 * Parse the incoming request in a RESTful way
	 *
	 * @param PASL_Web_Service_Request The request object
	 */
	public function parseRequest($oRequest)
	{

		$oRequestData = Array();

		switch($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				$oRequestHash = $_GET;
			break;
			case 'POST':
				$oRequestHash = $_POST;
			break;
			case 'PUT':
				parse_str(file_get_contents("php://input"), $oRequestHash);
			break;
		}

		$oRequest->requestPayload = $oRequestData;

		$oRequest->method = $oRequest->oRequestHash[2];

		// Grab the method arguments
		$methodArgs = $oRequest->oRequestHash;
		array_shift($methodArgs);
		array_shift($methodArgs);
		array_shift($methodArgs);

		$oRequest->methodArgs = $methodArgs;

		return $oRequest;
	}
}
?>