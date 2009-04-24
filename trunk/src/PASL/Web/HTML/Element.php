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

	class PASL_Web_DOM_Element
	{
		protected $AppendedElements = Array();

		protected $Attribute = Array();

		protected $TagName = '';

		protected $Readonly = false;

		protected $innerTagText = '';

		private $innerHTML = '';

		private $Appended = null;

		public function setAttribute($Name, $Value)
		{
			$Name = strtolower($Name);
			$this->Attribute[''.$Name.''] = $Value;
		}

		public function getAttribute($Name)
		{
			$Name = strtolower(trim($Name));

			return (!empty($this->Attribute[$Name])) ? $this->Attribute[$Name] : false;
		}

		public function setTagName($TagName)
		{
			$this->TagName = $TagName;
		}

		public function setInnerHTML($InnerHTML)
		{
			$this->innerHTML = $InnerHTML;
		}

		public function appendChild($Element)
		{
			// Check to see if $Element is this class or extends this class
			$this->AppendedElements[] = $Element;
		}

		public function setInnerTagText($innerTagText)
		{
			$this->innerTagText = $innerTagText;
		}

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