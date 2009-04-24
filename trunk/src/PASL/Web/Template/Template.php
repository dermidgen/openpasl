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

	/**
	 * Base class for templates
	 *
	 * @author Scott Thundercloud
	 */
	abstract class PASL_Web_Template
	{
		/**
		 * Function to interpret the template
		 */
		abstract protected function Interpret();

		/**
		 * File for requiring
		 *
		 * @access protected
		 */
		protected $File = '';

		/**
		 * Data buffer
		 *
		 * @access private
		 */
		private $DataBuffer = '';

		/**
		 * Variables to be injected into the template
		 *
		 * @access protected
		 */
		protected $Variables = Array();

		protected $TemplateBasePath = null;
		/**
		 * Set file to be interpreted
		 *
		 * @return void
		 */
		public function SetFile($File)
		{
			$this->File = $File;
		}

		/**
		 * Set the variables to be injected into the template.
		 *
		 * @param array Variables
		 */
		public function SetVariables(array $Variables)
		{
			foreach($Variables AS $key=>$val)
			{
				$this->Variables[$key] = $val;
			}
		}

		/**
		 * Set a variable to be injected into the template.
		 *
		 * @param array Variable
		 */
		public function AddVariable(array $Variable)
		{
			list($key, $val) = each($Variable);
			$this->Variables[$key] = $val;
		}

		/**
		 * Run the interpreter and set the data buffer.
		 *
		 * @return boolean
		 */
		private function RunInterpreter()
		{
			if($this->File == '') throw new Exception('File is not set');
			$this->DataBuffer = $this->Interpret();
			return true;
		}

		/**
		 * Run the interpreter and return the data buffer.
		 *
		 * @return string Data Buffer
		 */
		public function __toString()
		{
			$this->RunInterpreter();
			return $this->DataBuffer;
		}
	}
?>