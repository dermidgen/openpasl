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
 * Request object that acts as a standard object to encapsulate
 * service request information.
 *
 * @package PASL_Web
 * @subpackage PASL_Web_Service
 * @category Web
 * @author Danny Graham <good.midget@gmail.com>
 */
class PASL_Web_Service_Request
{
	/**
	 * The full signature of the request URI
	 *
	 * @var string
	 */
	public $requestURI = null;

	/**
	 * The request uri broken out into a hash
	 *
	 * @var array
	 */
	public $oRequestHash = Array();

	/**
	 * The type of service call this request uses
	 *
	 * @var string
	 */
	public $serviceType = 'REST';

	/**
	 * The class or object that contains the method to be called
	 *
	 * @var string
	 */
	public $operationClass = null;

	/**
	 * The path that the operation object resides. This is required
	 * for services to be published as an AMF service due to the
	 * way AMFPHP works.
	 *
	 * @var string
	 */
	public $operationClassPath = null;

	/**
	 * The method name to be called
	 *
	 * @var string
	 */
	public $method = null;

	/**
	 * A collection of arguments to be passed when the method is called
	 *
	 * @var unknown_type
	 */
	public $methodArgs = Array();

	/**
	 * The contents of $_GET, $_POST, or the phpinput stream
	 *
	 * @var array
	 */
	public $requestPayload = Array();

	/**
	 * Adds the passed data to the methodArgs collection
	 *
	 * @param mixed Argument data to be added to the method call
	 * @return void
	 */
	public function addMethodArg($arg)
	{
		$this->methodArgs[] = $arg;
	}
}
?>