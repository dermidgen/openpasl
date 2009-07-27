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

require_once('PASL/Data/Session.php');

class PASL_Web_Form
{
	/**
	 * The form items (elements)
	 */
	private $Items = Array();

	/**
	 * The form element attributes
	 */
	private $Attributes = Array();

	/**
	 * The request method.
	 */
	private $RequestMethod = 'post';

	/**
	 * The form data.
	 */
	private $FormData = Array();

	/**
	 * Unique ID
	 */
	private $Id;

	/**
	 * Validator class to handle errors
	 */
	private $Validator;

	/**
	 * Changes form items class name on error
	 */
	private $ErrorClassName = null;

	/**
	 * Set an attribute on the form element
	 *
	 * @param string Name
	 * @param string Value
	 */
	public function setAttribute($Name, $Value)
	{
		$Name = strtolower($Name);
		$Value = strtolower($Value);

		if($Name == 'method') $this->RequestMethod = $Value;

		$this->Attributes[$Name] = $Value;
	}

	public function setDataValidator(PASL_Data_Validation_iValidator $Validator)
	{
		$this->Validator = $Validator;
	}

	/**
	 * Add a form item object and associate a variable name
	 *
	 * @see PASL_Web_Form_Item_Input
	 * @param object FormItemObj
	 * @param string FormItemName
	 */
	public function addItem($FormItemObj, $FormItemName)
	{
//		$ObjInfo = new ReflectionObject($FormItemObj);
//		$ParentClass = $ObjInfo->getParentClass()->getName();

//		if($ParentClass != 'PASL_Web_Form_Item_Common') throw new Exception('Invalid item object.  The object must extend '.$ParentClass);

		$Item = new stdClass;
		$Item->Name = $FormItemName;
		$Item->Item = $FormItemObj;

		$this->Items[] = $Item;
	}

	/**
	 * Set the template for the form to use
	 *
	 * @see PASL_Web_Template
	 * @param object Template
	 */
	public function setTemplate($Template)
	{
		$ObjInfo = new ReflectionObject($Template);
		$ParentClass = $ObjInfo->getParentClass()->getName();

		if($ParentClass != 'PASL_Web_Template') throw new Exception('Invalid template object.  The object must extend '.$ParentClass);

		$this->Template = $Template;
	}

	/**
	 * Triggers the submit action for each form item
	 */
	private function triggerSubmitAction()
	{
		$FormData = $this->FormData;

		foreach($this->Items AS $Item)
		{
			$I = $Item->Item;

			$ItemName = $I->getName();
			$RequestData = $FormData[$ItemName];

			$I->doSubmitAction($ItemName, $RequestData);
		}
	}

	/**
	 * Sets the form id
	 *
	 * @param $Id
	 * @return void
	 */
	public function SetId($Id)
	{
		$this->Id = $Id;
	}

	/**
	 * Get the form id
	 *
	 * @return void
	 */
	public function GetId()
	{
		return $this->Id;
	}

	/**
	 * Set the form data
	 *
	 * @param array $FormData
	 * @return void
	 */
	public function SetFormData($FormData)
	{
		$this->FormData = $FormData;
	}

	/**
	 * Sets the error class name.  If an error occurres on an item, the item's class name is changed
	 * to the specified class name.
	 *
	 * @param $ErrorClassName
	 * @return void
	 */
	public function SetErrorClassName($ErrorClassName)
	{
		$this->ErrorClassName = $ErrorClassName;
	}

	/**
	 * Returns the form HTML
	 *
	 * @return string FormHTML
	 */
	public function __toString()
	{
		if(!empty($this->FormData))
		{
			$this->triggerSubmitAction();
		}

		$Variables = Array();
		foreach($this->Items AS $Item)
		{
			$I = $Item->Item;
			$Name = $I->getName();

			$Variables[$Item->Name] = (string) $I;

			if(!empty($this->Validator))
			{
				$Error = $this->Validator->getErrorByName($Name);

				if($Error)
				{
					$I->setAttribute('class', $this->ErrorClassName);
					$Variables[$Error->Name.'_error'] = (is_array($Error->Message)) ? end($Error->Message) : $Error->Message;
				}
			}
		}

		if(!empty($this->Validator))
		{
			if($this->Validator->isError())
			{
				$Errors = $this->Validator->getErrors();
				$Variables['first_error'] = $Errors[0]->Message;
			}
		}

		$this->Template->setVariables($Variables);

		$string = '';
		$i=0;
		foreach($this->Attributes AS $Name=>$Value)
		{
			$string .= $Name.'="'.$Value.'"';
			if(count($this->Attributes)-1 != $i) $string .= ' ';

			$i++;
		}

		$FormHTML = '<form '.$string.'>' . "\n";
		$FormHTML .= '<input type="hidden" value="1" name="'.$this->Id.'">';
		$FormHTML .= (string) $this->Template . "\n";

		$FormHTML .= '</form>' . "\n";

		return $FormHTML;
	}
}
?>