<?
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

namespace PASL\Web\API\AuthorizeNet\Response;

/**
 * Helper class for handling an AIM response from authorize.net
 * Refer to the authorize.net documentation for more information
 * 
 * @category authorize.net
 * @package PASL\Web\API\AuthorizeNet\Response
 * @author Scott Thundercloud <scott.tc@gmail.com>
 */
class AIM
{
	/**
	 * @var string
	 */
	protected $Response = null;
	
	/**
	 * Sets the response 
	 * 
	 * @param string $Response
	 * @return void
	 */
	public function __construct($Response)
	{
		$this->setResponse($Response);
	}
	
	/**
	 * Sets the response 
	 * 
	 * @param string $Response
	 * @return void
	 */
	public function setResponse($Response)
	{
		$this->Response = explode(',', $Response);
	}
	
	/**
	 * Return the response 
	 * 
	 * @return string
	 */
	public function getResponse()
	{
		return $this->Response;
	}
	
	/**
	 * Returns the response code
	 * 
	 * @return string
	 */
	public function getCode()
	{
		$Response = $this->getResponse();
		$Code = $Response[0];
		
		return $Code;
	}
	
	/**
	 * Returns the reason text
	 * 
	 * @return string
	 */
	public function getReasonText()
	{
		$Response = $this->getResponse();
		$Reason = $Response[3];
		
		return $Reason;
	}
	
	/**
	 * Checks to see if the request was valid
	 * 
	 * @return boolean
	 */
	public function isValid()
	{
		$Response = $this->getResponse();
		$Code = $Response[0];
		
		if($Code == 1) return true;
		
		return false;
	}
	
	/**
	 * Checks to see if the credit card credentials were declined
	 * 
	 * @return boolean
	 */
	public function isDeclined()
	{
		$Response = $this->getResponse();
		$Code = $Response[0];
		
		if($Code == 2) return true;
		
		return false;
	}
}
?>