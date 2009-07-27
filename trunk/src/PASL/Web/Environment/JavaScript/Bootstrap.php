<?
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

require_once('PASL/Web/Environment/aEnvironment.php');
require_once('PASL/Interpreter/JavaScript/SpiderMonkey.php');




class PASL_Web_Environment_JavaScript_Bootstrap extends PASL_Web_aEnvironment
{
	protected $Options;
	protected $Interpreter;
	protected $IndexFile = 'index.js';
	protected $BootstrapDirectory = '.';
	protected $EnvironmentImports = Array();
	
	public function __construct()
	{
		$this->setInterpreter(new PASL_Interpreter_JavaScript_SpiderMonkey);
		
		$Interpreter = $this->getInterpreter();
	}
	
	public function registerClass($phpClassName, $jsObjectName)
	{
		$Obj = new stdClass;
		$Obj->phpName = $phpClassName;
		$Obj->jsObjectName = $jsObjectName;
		$Obj->Type = 'class';

		$this->EnvironmentImports[] = $Obj;

		return $Obj;
	}
	
	public function registerFunction($phpFunctionName, $jsObjectName)
	{
		$Obj = new stdClass;
		$Obj->phpName = $phpFunctionName;
		$Obj->jsObjectName = $jsObjectName;
		$Obj->Type = 'function';

		$this->EnvironmentImports[] = $Obj;
		
		return $Obj;
	}
	
	public function registerObject($name, $value)
	{
		$Obj = new stdClass;
		$Obj->phpName = $name;
		$Obj->jsObjectName = $value;
		$Obj->Type = 'object';


		$this->EnvironmentImports[] = $Obj;
		
		return $Obj;
	}
	
	protected function GenerateBootstrap()
	{
		$Interpreter = $this->getInterpreter();
		
		$bootstrap_str = '';
		$jsobjstr = '';
		foreach($this->EnvironmentImports AS $Import)
		{
			$php_class_name = $Import->phpName;
			$js_object_name = $Import->jsObjectName;
			
			$explodestr = (!is_string($js_object_name)) ? $php_class_name : $js_object_name;

			// Register JS Object
			$explode = explode(".", $explodestr);

			$str = '';
			$i=0;
			foreach($explode AS $jsobj)
			{
				if($i == 0) $str .= $jsobj . "";
				else $str .= '.' . $jsobj;

				$i++;
				if(preg_match("/".$str."/", $jsobjstr)) continue;

				$jsobjstr .= $str . " = {}; \n";
			}
			
			switch($Import->Type)
			{
				case 'function':
					$Interpreter->registerFunction($php_class_name, $php_class_name);
				break;
				
				case 'class':
					$Interpreter->registerClass($php_class_name, $php_class_name);
				break;
				
				case 'object':
					$Interpreter->registerObject($php_class_name, $js_object_name);
					$js_object_name = $php_class_name;
				break;
				
				default:
					continue;
				break;
			}

			$jsobjstr .= $js_object_name .= ' = ' . $php_class_name . ";\n";
			$jsobjstr .= $php_class_name . ' = null;' . "\n";
		}

		$bootstrap_str .= $jsobjstr;
		
		var_dump($bootstrap_str);
		
		return $bootstrap_str;
	}
}
?>