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

	require_once("PASL/Web/HTML/Element.php");

	abstract class PASL_Web_Form_Item_Common extends PASL_Web_DOM_Element
	{
		# abstract function __toString(); // Satisfied by PASL_Web_DOM_Element
		abstract function doSubmitAction($Name, $Value);

		protected $internalData = '';

		private $Validator = '';

		private $Error = Array();

		private $Static = false;

		/**
		 * Sets the validator for the form object.
		 * Accepts an array to reference an object or a string to reference function name.
		 *
		 * @example
		 * $FormObj->setValidator(array($object=>'method'));
		 * $FormObj->setValidator('function_name');
		 *
		 * @param array|string $Validator
		 * @return void
		 */
		public function setValidator($Validator)
		{
			$this->Validator = $Validator;
		}

		public function getErrorMessage()
		{
			return $this->Error;
		}

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
		 * Validates the element
		 *
		 * @return bool
		 */
		public function Validate()
		{
			if(!$this->Validator) return true;

			if(!is_array($this->Validator))
			{
				$Function = new ReflectionFunction($this->Validator);
				$Data = $Function->invoke($this->getName(), $this->getValue());
			}
			else
			{
				list($ObjRef, $MethodName) = $this->Validator;
				$Data = $ObjRef->{$MethodName}($this->getName(), $this->getValue());
			}


			if(is_bool($Data)) return true;
			else
			{
				foreach($Data AS $Error)
				{
					$this->addErrorMessage($Error);
				}
				return false;
			}
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

		public function setName($Name)
		{
			$this->setAttribute('name', $Name);
		}

		public function setID($ID)
		{
			$this->setAttribute('id', $Name);
		}

		public function getName()
		{
			return $this->getAttribute('name');
		}

		public function getValue()
		{
			return $this->internalData;
		}

		public function getValidator()
		{
			return $this->Validator;
		}

		public function setClassName($ClassName)
		{
			$this->setAttribute('class', $ClassName);
		}

		public function getClassName()
		{
			return $this->getAttribute('class');
		}
	}
?>