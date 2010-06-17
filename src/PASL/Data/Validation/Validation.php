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

namespace PASL\Data;

/**
 * Abstract class for classes wishing to validate a data set
 * 
 * @package PASL_Data_Validation
 * @category Data Validation
 * @author Scott Thundercloud <scott.tc@gmail.com>
 */
abstract class Validation
{
	/**
	 * Each class will have its own way of validating a data set
	 * 
	 * @return boolean
	 */
	abstract function Validate();

	/** 
	 * @var boolean
	 */
	private $Validated = false;
	
	/**
	 * @var string
	 */
	private $Data = null;
	
	/**
	 * @var array
	 */
	private $Errors = Array();
	
	/**
	 * Add an error
	 * 
	 * @param string $Value
	 * @return void
	 */
	protected function addError($Value)
	{
		$this->Errors[] = $Value;
	}
	
	/**
	 * Set the data to validate
	 * 
	 * @param mixed $Data
	 * @return void
	 */
	public function setData($Data)
	{
		$this->Data = $Data;
	}

	/**
	 * Return the data
	 * 
	 * @return mixed
	 */
	public function getData()
	{
		return $this->Data;
	}
	
	/**
	 * Return the errors
	 * 
	 * @return array
	 */
	public function getErrors()
	{
		return $this->Errors;
	}
	
	/**
	 * Checks to see if an error occurred
	 * 
	 * @return void
	 */
	public function isError()
	{
		if(count($this->Errors) > 0) return true;
		else return false;
	}
	
	/**
	 * 
	 * @param $validated
	 * @return unknown_type
	 */
	public function setValidated($validated)
	{
		$this->Validated = $validated;
	}
	
	/**
	 * Return an error by name
	 * 
	 * @param string $ErrorName
	 * @return string|false
	 */
	public function getErrorByName($ErrorName)
	{
		foreach($this->Errors AS $Error)
		{
			if($Error->Name == $ErrorName) return $Error;
		}
		return false;
	}

	/**
	 * Checks to see if the data set is validated
	 * 
	 * @return boolean
	 */
	public function isValidated()
	{
		return $this->Validated;
	}
}

?>