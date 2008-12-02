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


	require_once('PASL/Web/Template/Template.php');

	class PASL_Web_Template_Type_Token extends PASL_Web_Template
	{
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
				if (isset($this->Variables[$tokens[$i]])) $body = str_replace("%{$tokens[$i]}%", $this->Variables[$tokens[$i]], $body);
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

			if ($local)
			{
				$file = array();

				$file = file(($template) ? $this->TemplateBasePath.$url : $url);

				foreach ($file as $line)
				{
					$body .= $line;
				}
			}
			else
			{
				$fp = fopen($url,"r");
				$body = fread($fp,1000000);
				fclose($fp);
			}

			$body = $this->parseData($body);
			return $body;
		}

		/**
		 * Interpret the file.
		 *
		 * @return string Template Data
		 */
		protected function Interpret()
		{
			return $this->loadAndParse($this->File);
		}
	}
?>