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

/**
 * Page provides the base class for anything that's going out to the browser
 *
 * @package PASL_Web
 * @subpackage PASL_Web_Simpl
 * @category Web
 * @author Danny Graham <good.midget@gmail.com>
 */
class PASL_Web_Simpl_Page
{
	/**
	 * @var Array
	 */
	public $TOKENS = Array();

	/**
	 * @var String
	 */
	public $Theme = "default";

	/**
	 * @var String
	 */
	public $TemplateBasePath = "themes/default/templates/";

	/**
	 * References MainNav
	 *
	 * @var PASL_Web_Simpl_MainNav
	 */
	public $MainNav;

	/**
	 * References SubNav
	 *
	 * @var PASL_Web_Simpl_SubNav
	 */
	public $SubNav;

	/**
	 * References UserNav
	 *
	 * @var PASL_Web_Simpl_UserNav
	 */
	public $UserNav;

	/**
	 * @var String
	 */
	public $PageTitle;

	/**
	 * @var String
	 */
	public $SectionTitle;

	/**
	 * @var String
	 */
	public $PageTemplate = "body_template.html";

	/**
	 * @var String
	 */
	public $Body;

	/**
	 * @var String
	 */
	public $JSPayload;

	/**
	 * @var String
	 */
	public $JSScriptPayload;

	/**
	 * @var String
	 */
	public $CSSPayload;

	/**
	 * @var String
	 */
	public $CSSScriptPayload;

	/**
	 * @var Array
	 */
	private $JSPackages = Array();

	/**
	 * @var Array
	 */
	private $JSScriptPackages = Array();

	/**
	 * @var Array
	 */
	private $CSSPackages = Array();

	/**
	 * @var Array
	 */
	private $CSSScriptPackages = Array();

	/**
	 * Updates the JSPlayload with all included scripts
	 */
	private function updateJSPayload($url)
	{
		$this->JSPayload = '';
		foreach($this->JSPackages as $package)
		{
			$this->JSPayload .= '<script type="text/javascript" src="'.$url.'"></script>' . "\n";
		}
	}

	/**
	 * Adds a path for inclusion via <script> tags
	 * @param String $url
	 */
	public function addJSPackage($url)
	{
		array_push($this->JSPackages, $url);
		$this->updateJSPayload($url);
	}

	/**
	 * Updates JavaScript blocks
	 */
	public function updateJSScriptPayload()
	{
		$this->JSScriptPayload = '<script type="text/javascript">'."\n";

		foreach($this->JSScriptPackages as $package)
		{
			$this->JSScriptPayload .= $package . "\n";
		}

		$this->JSScriptPayload .= "</script>\n";
	}

	/**
	 * Adds block of JavaScript code surrounded by <script> tags
	 * @param String $script
	 */
	public function addJSScriptPackage($script)
	{
		array_push($this->JSScriptPackages, $script);
		$this->updateJSScriptPayload();
	}

	/**
	 * Updates the CSSPlayload with all included scripts
	 */
	private function updateCSSPayload($url)
	{
		$this->CSSPayload = '';
		foreach($this->CSSPackages as $package)
		{
			$this->CSSPayload .= '<link href="'.$url.'" type="text/css" rel="stylesheet">' . "\n";
		}
	}

	/**
	 * Add a CSS package path for inclusion
	 *
	 * @param string The url to a CSS file
	 */
	public function addCSSPackage($url)
	{
		array_push($this->CSSPackages, $script);
		$this->updateCSSPayload($url);
	}

	/**
	 * Updates the CSS Script Payload containing CSS markup
	 */
	public function updateCSSScriptPayload()
	{
		$this->CSSScriptPayload = '<style type="text/css">'."\n";

		foreach($this->CSSScriptPackages as $package)
		{
			$this->CSSScriptPayload .= $package . "\n";
		}

		$this->CSSScriptPayload .= "</style>\n";
	}

	/**
	 * Add CSS script block for inclusion
	 * @param String $script
	 */
	public function addCSSScriptPackage($script)
	{
		array_push($this->CSSScriptPackages, $script);
		$this->updateCSSScriptPayload();
	}

	/**
	 * Global Token Replacement For Specified Data
	 *
	 * @param String $data
	 * @return String
	 */
	private function parseData($data)
	{
		$body = $data;

		preg_match_all( "/\%(\S+?)\%/", $body, $matches );
		$tokens = array_values( array_unique( array_values( $matches[1] ) ) );

		for ($i = 0; $i < count($tokens); $i++)
		{
			if (isset($this->TOKENS[$tokens[$i]]))	$body = str_replace("%{$tokens[$i]}%", $this->TOKENS[$tokens[$i]], $body);
			else $body = str_replace("%{$tokens[$i]}%", "", $body);
		}
		return $body;
	}

	/**
	 * Return files parsed for Tokens.
	 *
	 * @param String $url Path to the doc fragment that needs to be parsed
	 * @param Bool $template Is the path to a template that requires the TemplateBasePath.  Defaults to true
	 * @return String
	 */
	public function loadAndParse($url, $template=true)
	{
		ereg("http://",$url) ? $local = FALSE : $local = TRUE;

		$body = "";
		
		if($template) $templateURI = (file_exists($this->TemplateBasePath.$url)) ? $this->TemplateBasePath.$url : "themes/default/templates/{$url}";

		if ($local)
		{
			$file = array();
			$file = file(($template) ? $templateURI : $url);
			foreach ($file as $line)
			{
				$body .= $line;
			}
		} else
		{
			$fp = fopen($url,"r");
			$body = fread($fp,1000000);
			fclose($fp);
		}

		$body = $this->parseData($body);
		return $body;
	}

	/**
	 * Displays the page
	 */
	public function display()
	{
		print '<?xml version="1.0" encoding="UTF-8" ?>';
		require("themes/{$this->Theme}/{$this->PageTemplate}");
		exit;
	}
}

?>
