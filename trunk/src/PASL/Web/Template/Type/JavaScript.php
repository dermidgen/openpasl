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

namespace PASL\Web\Template\Type;
 
require_once('PASL/Web/Template/Template.php');
require_once('PASL/Interpreter/JavaScript/SpiderMonkey.php');
require_once('PASL/Log.php');

use PASL\Web\Template;
use PASL\Interpreter\JavaScript\SpiderMonkey;
use PASL\Log;

/**
 * Class that provides an interface to create a web template using the JavaScript language
 * 
 * !!!!! THIS CLASS REQUIRES THE PECL-SPIDERMONKEY PACKAGE !!!!!
 * 
 * Find it at http://pecl.php.net/package/spidermonkey/
 * More information at http://www.bombstrike.org/2009/02/bringing-javascript-to-the-server/
 * 
 * @package PASL_Web_Template_Type
 * @category JavaScript Interpeter
 * @author Scott Thundercloud <scott.tc@gmail.com>
 */


function __php_print($input)
{
	print $input;
}

class JavaScript extends Template
{
	private $JSInterpreter = null;

	protected function Interpret()
	{
		$JSInterpreter = $this->getJSInterpreter();
		$JSInterpreter->registerFunction('__php_print', 'print');

		foreach($this->Variables AS $key=>$val)
		{
			$JSInterpreter->assignVariable($key, $val);
		}

		$JSInterpreter->setFile($this->File);
		$Content = (string) $JSInterpreter->Run();

		return $Content;
	}
	
	public function getJSInterpreter()
	{
		return $this->JSInterpreter;
	}
	
	public function setJSInterpreter(PASL\Interpreter\iInterpreter $Interpreter)
	{
		$this->JSInterpreter = $Interpreter;
	}
}
?>