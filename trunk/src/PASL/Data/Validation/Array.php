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

require_once('PASL/Data/Validation/Validation.php');
require_once('PASL/Log.php');
require_once('PASL/Data/Validation/iValidator.php');
require_once('PASL/Data/Validation/Error.php');

class PASL_Data_Validation_Array extends PASL_Data_Validation implements PASL_Data_Validation_iValidator
{
	private $Callback = Array();
	private $GlobalCallback = null;

	public function addCallback($Key, $Callback)
	{
		$this->Callback[$Key] = $Callback;
	}

	public function setGlobalCallback($Callback)
	{
		$this->GlobalCallback = $Callback;
	}

	public function Validate()
	{
		$Data = $this->getData();

		foreach($Data AS $key=>$val)
		{
			$Callback = (!empty($this->Callback[$key])) ? $this->Callback[$key] : ($this->GlobalCallback !== null) ? $this->GlobalCallback : false;
			if($Callback === false)
			{
				PASL::Log(__CLASS__.': Callback not set for "{$key}"');
				continue;
			}

			if(is_array($Callback))
			{
				$obj = $Callback[0];
				$method = $Callback[1];

				// Method
				if(!is_object($obj))
				{
					$Object = new ReflectionClass($obj);
					$obj = $Object->newInstance();
				} else $Object = new ReflectionObject($obj); // Object

				$Method = $Object->getMethod($method);
				$Data = $Method->invoke($obj, $key, $val);
			}
			else
			{
				// Run function
				$Function = new ReflectionFunction($Callback);
				$Data = $Function->invoke($key, $val);
			}

			if($Data !== true)
			{
				$Error = new PASL_Data_Validation_Error;
				$Error->Message = $Data;
				$Error->Name = $key;
				$Error->Value = $val;

				$this->addError($Error);
			}
		}

		if(count($this->getErrors()) == 0)
		{
			$this->setValidated(true);
			return true;
		}
		else
		{
			$this->setValidated(false);
			return false;
		}
	}
}
?>