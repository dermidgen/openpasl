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

namespace PASL\Web\HTML\Element;

require_once("PASL/Web/HTML/Element.php");

use PASL\Web\HTML\Element;

/**
 * Form element base class
 * 
 * @package \PASL\Web\Form\Item
 * @author Scott Thundercloud
 */

abstract class Common extends Element
{
	# abstract function __toString(); // Satisfied by PASL_Web_DOM_Element
	abstract function doSubmitAction($Name, $Value);

	/**
	 * The internal data buffer
	 * 
	 * @var string
	 */
	protected $internalData = '';
	
	/**
	 * 
	 * @var unknown_type
	 */
	private $Static = false;

	/**
	 * Checks if the value of the element is static or not
	 *
	 * @return bool
	 */
	public function isStatic()
	{
		if($this->Static === true) return true;
		else return false;
	}

	/**
	 * Sets the element's value as static
	 */
	public function setStatic($Static)
	{
		$this->Static = $Static;
	}

	/**
	 * Adds an error message
	 */
	private function addErrorMessage($Error)
	{
		$this->Error[] = $Error;
	}

	/**
	 * Sets the elements value and internal data
	 */
	public function setValue($Value)
	{
		$this->internalData = $Value;
		$this->setAttribute('value', $Value);
	}

	/**
	 * Sets the name for the element
	 * 
	 * @param string $Name
	 * @return void
	 */
	public function setName($Name)
	{
		$this->setAttribute('name', $Name);
	}
	
	/**
	 * Sets the ID for the element
	 * 
	 * @param string $ID
	 * @return void
	 */
	public function setID($ID)
	{
		$this->setAttribute('id', $Name);
	}

	/**
	 * Gets the name of the element
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->getAttribute('name');
	}

	/**
	 * Returns the value of the element
	 * 
	 * @return string
	 */
	public function getValue()
	{
		return $this->internalData;
	}

	/**
	 * Sets the class name of the element
	 * 
	 * @param string $ClassName
	 * @return void
	 */
	public function setClassName($ClassName)
	{
		$this->setAttribute('class', $ClassName);
	}

	/**
	 * Gets the class name of the element
	 * 
	 * @return string
	 */
	public function getClassName()
	{
		return $this->getAttribute('class');
	}
}
?>