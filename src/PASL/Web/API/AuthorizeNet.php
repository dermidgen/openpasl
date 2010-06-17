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

namespace PASL\Web\API;

/**
 * A facade class to handle an authorize.net query
 * 
 * @category authorize.net
 * @package PASL\Web\API
 */
class AuthorizeNet
{
	/**
	 * The authorize.net gateway URI
	 * 
	 * @var string
	 */
	private $Gateway = '';
	
	/**
	 * API Login credential
	 * 
	 * @var string
	 */
	protected $APILogin = '';
	
	/**
	 * @var string
	 */
	protected $TransactionKey = '';
	
	/**
	 * @var array
	 */
	protected $Payload = array();
	
	/**
	 * @var string
	 */
	protected $Request;
	
	/**
	 * Sets the request type
	 * 
	 * @param object $Request
	 * @return void
	 */
	public function setRequest($Request)
	{
		$this->Request = $Request;
	}
	
	/**
	 * Set the payment gateway URI
	 * 
	 * @param string $Gateway
	 * @return void
	 */
	public function setGateway($Gateway)
	{
		$this->Gateway = $Gateway;
	}
	
	/**
	 * Return the gateway
	 * 
	 * @return string
	 */
	public function getGateway()
	{
		return $this->Gateway;
	}

	/**
	 * Execute a request to the gateway
	 * 
	 * @return string
	 */
	public function Execute()
	{
		$queryString = (string) $this->Request;

		$options = array
		(
			CURLOPT_URL => $this->getGateway(),
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => $queryString,
			CURLOPT_RETURNTRANSFER => 1
		);
		
		$ch = curl_init();
		curl_setopt_array($ch, $options);
		$returnPayload = curl_exec($ch);

		curl_close($ch);
		
		return $returnPayload;
	}
}
?>