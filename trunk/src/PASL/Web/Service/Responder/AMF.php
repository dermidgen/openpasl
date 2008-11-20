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

require_once('PASL/Web/Service/iServiceResponder.php');

/**
 * This is a shell class to meet the requirements of a provider and a responder for each
 * service type.  In the case of AMF services we hand off to AMFPHP to handle the
 * reponse packaging - so we never get to this class.
 *
 * @package PASL_Web_Service
 * @subpackage PASL_Web_Service_Responder
 * @category Web
 * @author Danny Graham <good.midget@gmail.com>
 */
class PASL_Web_Service_Responder_AMF implements PASL_Web_Service_iServiceResponder
{
	private $response = null;

	/**
	 * Clears any data in the current response buffer
	 *
	 * @return void
	 */
	public function clearResponseBuffer()
	{
	}

	/**
	 * Get's the response payload ready for transport to the service client
	 *
	 * @return string Fully formatted response payload (XML, JSON, AMF)
	 */
	public function getResponse()
	{
	}

	/**
	 * Adds data to the response buffer
	 *
	 * @param mixed Data to add to the output buffer
	 * @return void
	 */
	public function addPayload($payload)
	{
	}
}
?>