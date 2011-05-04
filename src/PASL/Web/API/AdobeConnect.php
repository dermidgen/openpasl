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
 * This class is an interface for interacting with Adobe Connect
 * 
 * @category adobe-connect
 * @package PASL\Web\API
 */

class AdobeConnect
{
	/**
	 * The Adobe Connect URL
	 * 
	 * @var string
	 */
	protected $URL = '';
	
	
	/**
	 * Set the URL
	 * 
	 * @param string $URL
	 */
	public function __construct($URL)
	{
		$this->setURL($URL);
	}
	
	
	/**
	 * Sends a command to the Adobe Connect web service
	 * 
	 * @param array $options
	 * @return XML
	 */
	protected function sendCommand($options)
	{
		$get = http_build_query($options);
		
		$URL = $this->URL . '?' . $get;
		
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $URL);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		return curl_exec($curl);
	}
	
	/**
	 * Sets the URL
	 * 
	 * @param string $URL
	 */
	public function setURL($URL)
	{
		$this->URL = $URL;
	}
	
	/**
	 * Login to the web service
	 * 
	 * @param string $username
	 * @param string $password
	 * @return XML
	 */
	public function login($username, $password)
	{
		$options = array(
			'action'   => 'login',
			'login'    => $username,
			'password' => $password
		);
		
		$response = $this->sendCommand($options);
		
		return $response;
	}
	
	/**
	 * Create a new user
	 * 
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $login
	 * @param string $password
	 * @param string $emailAddress
	 * @param string $type
	 * @param boolean $sendEmail
	 * @param int $hasChildren
	 * @return XML
	 */
	public function createUser($firstName, $lastName, $login, $password, $emailAddress, $type = 'user', $sendEmail = true, $hasChildren = 0)
	{
		$options = array(
			'action'      => 'principal-update',
			'first-name'  => $firstName,
			'last-name'   => $lastName,
			'login'       => $login,
			'password'    => $password,
			'type'        => $type,
			'send-email'  => $sendEmail,
			'hasChildren' => $hasChildren,
			'email'       => $emailAddress
		);
		
		$response = $this->sendCommand($options);
		
		return $response;
	}
	
	/**
	 * Update a user
	 * 
	 * @param array $argOptions
	 * @return XML
	 */
	public function updateUser($argOptions)
	{
		$options = array(
			'action'       => 'principal-update',
			'principal-id' => $options['principal-id']
		);
		
		
		foreach($argOptions AS $key => $val)
		{
			$options[$key] = $val;
		}
		
		$response = $this->sendCommand($options);
		
		return $response;
	}
	
	/**
	 * Get a principal ID by their login username
	 * 
	 * @param string $login
	 * @return int $principalId
	 */
	public function getPrincipalIDByLogin($login)
	{
		$options = array(
			'action'       => 'principal-list',
			'filter-login' => $login
		);
		
		$response = $this->sendCommand($options);
		
		$doc = new DOMDocument();
		$doc->loadXML($response);
		
		$elements = $doc->getElementsByTagName('principal');
		$element = $elements[0];
		
		$principalId = $element->getAttribute('principal-id');
		
		return $principalId;
	}
	
}

?>
