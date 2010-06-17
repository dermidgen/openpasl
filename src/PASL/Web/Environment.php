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
namespace PASL\Web\Environment;


/**
 * An experiment attempting to create a self containted environment inside PHP
 * 
 * @package \PASL\Web
 * @category Experimental
 * @author Scott Thundercloud <scott.tc@gmail.com>
 */
abstract class Environment
{
	/**
	 * The interpreter
	 * 
	 * @var Object
	 */
	protected $Interpreter;
	
	/**
	 * The index file
	 * 
	 * @var object
	 */
	protected $IndexFile = 'index.js';
	
	/**
	 * The environment directory
	 * 
	 * @var string
	 */
	protected $EnvironmentDirectory = null;
	
	/**
	 * The config file
	 * 
	 * @var string
	 */
	protected $ConfigFile = null;


	/**
	 * Sets the interpreter
	 * 
	 * @param $Interpreter
	 * @return void
	 */
	protected function setInterpreter($Interpreter)
	{
		$this->Interpreter = $Interpreter;
	}
	
	/**
	 * Returns the assigned interpreter
	 * 
	 * @return void
	 */
	protected function getInterpreter()
	{
		return $this->Interpreter;
	}
	
	/**
	 * Sets the index(bootstrap) filename
	 * 
	 * @param string $IndexFile
	 * @return void
	 */
	public function setIndexFile($IndexFile)
	{
		$this->IndexFile = $IndexFile;
	}

	/**
	 * Sets the path where the enviroment lies in
	 * 
	 * @param string $EnvironmentDirectory
	 * @return unknown_type
	 */
	public function setEnvironmentDirectory($EnvironmentDirectory)
	{
		$this->EnvironmentDirectory = $EnvironmentDirectory;
	}

	/**
	 * Sets the configuration file
	 * 
	 * @param string $configFile
	 * @return void
	 */
	public function setConfigFile($configFile)
	{
		$this->configFile = $configFile;
	}

	/**
	 * Returns the configuration file
	 * 
	 * @return unknown_type
	 */
	public function getConfigFile()
	{
		return $this->configFile;
	}

	/**
	 * Runs the environment
	 * 
	 * @return string
	 */
	public function Run()
	{
		$Interpreter = $this->getInterpreter();
		
		$bootstrap_contents = $this->GenerateBootstrap();
		$script_contents = file_get_contents($this->EnvironmentDirectory . '/' . $this->IndexFile);
		
		$contents = $bootstrap_contents . $script_contents;

		$Output = $Interpreter->evaluateScript($contents);
		
		return $Output;
	}
}
?>