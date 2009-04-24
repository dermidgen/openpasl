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
	 * The form errors from item objects
	 */
	private $Error = Array();

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
	 * Handles validation on each input
	 *
	 * @return boolean
	 */
	public function Validate()
	{
		$Items = $this->Items;

		foreach($Items AS $Item)
		{
			$I = $Item->Item;
			if(!$I->Validate())
			{
				$Name = $I->getName();
				$this->Error[$Name] = $I->getErrorMessage();
			}
		}
	}

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

	/**
	 * Returns whether or not the form is validated.
	 *
	 * @return boolean
	 */
	public function isValidated()
	{
		if(count($this->Error) >= 1) return false;
		return true;
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
		$ObjInfo = new ReflectionObject($FormItemObj);
		$ParentClass = $ObjInfo->getParentClass()->getName();

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

	public function SetId($Id)
	{
		$this->Id = $Id;
	}

	public function GetId()
	{
		return $this->Id;
	}

	public function SetFormData($FormData)
	{
		$this->FormData = $FormData;
	}

	/**
	 * Returns the form HTML
	 *
	 * @return string FormHTML
	 */
	public function __toString()
	{
		if(!empty($this->FormData[$this->GetId()]))
		{
			$this->triggerSubmitAction();
			$this->Validate();
		}


		$Variables = Array();
		foreach($this->Items AS $Item)
		{
			$I = $Item->Item;
			$Name = $I->getName();

			$Variables[$Item->Name] = (string) $I;

			if(!empty($this->Error[$Name])) $Variables[$Name.'_error'] = end($this->Error[$Name]);
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