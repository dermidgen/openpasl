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

namespace PASL\Web\HTML;

/**
 * Helper class to build a basic HTML Element
 * 
 * @package \PASL\Web\HTML
 * @author Scott Thundercloud
 */
class Element
{
	/**
	 * The appended elements
	 * 
	 * @var array
	 */
	protected $AppendedElements = Array();

	/**
	 * The attributes for each element
	 * 
	 * @var array
	 */
	protected $Attribute = Array();

	/**
	 * The element tag name
	 * @var string
	 */
	protected $TagName = '';

	/**
	 * Is the element read only?
	 * 
	 * @var boolean
	 */
	protected $Readonly = false;

	/**
	 * The elements inner tag text ie <option name="foobar" selected>
	 * 
	 * @var string
	 */
	protected $innerTagText = '';
	
	/**
	 * The elements inner html
	 * 
	 * @var string
	 */
	private $innerHTML = '';

	/**
	 * The appended elements
	 * 
	 * @var string
	 */
	private $Appended = null;

	/**
	 * Sets the attribute for the element
	 * 
	 * @param string $Name
	 * @param string $Value
	 * @return void
	 */
	public function setAttribute($Name, $Value)
	{
		$Name = strtolower($Name);
		$this->Attribute[''.$Name.''] = $Value;
	}

	/**
	 * Get an attribute of the element
	 * 
	 * @param string $Name
	 * @return void
	 */
	public function getAttribute($Name)
	{
		$Name = strtolower(trim($Name));

		return (!empty($this->Attribute[$Name])) ? $this->Attribute[$Name] : false;
	}

	/**
	 * Set the tag name
	 * 
	 * @param string $TagName
	 * @return void
	 */
	public function setTagName($TagName)
	{
		$this->TagName = $TagName;
	}
	
	/**
	 * Set the innerhtml for the element
	 * 
	 * @param string $InnerHTML
	 * @return void
	 */
	public function setInnerHTML($InnerHTML)
	{
		$this->innerHTML = $InnerHTML;
	}

	/**
	 * Append a child to the element 
	 * 
	 * @param object $Element
	 * @return void
	 */
	public function appendChild($Element)
	{
		// Check to see if $Element is this class or extends this class
		$this->AppendedElements[] = $Element;
	}

	/**
	 * Set the inner tag text
	 * 
	 * @param string $innerTagText
	 * @return void
	 */
	public function setInnerTagText($innerTagText)
	{
		$this->innerTagText = $innerTagText;
	}

	/**
	 * Compiles each child and element to a string
	 * 
	 * @return string
	 */
	public function __toString()
	{
		$html = '<'.$this->TagName.' ';

		$i=0;
		foreach($this->Attribute AS $Name=>$Value)
		{
			$html .= $Name . '="'.$Value.'"';
			if($i <= count($this->Attribute)) $html .= ' ';

			$i++;
		}

		$html .= ''.$this->innerTagText.'>';

		if(count($this->AppendedElements) >= 1)
		{
			foreach($this->AppendedElements AS $AppendedElement)
			{
				$html .= (string) $AppendedElement;
			}
		}

		if(!empty($this->innerHTML)) $html .= $this->innerHTML;
		if(!empty($this->innerHTML) || count($this->Appended >= 1)) $html .= '</'.$this->TagName.'>';

		return $html;
	}
}
?>