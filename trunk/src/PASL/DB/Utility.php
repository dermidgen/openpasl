<?php
/**
 * @license <http://www.opensource.org/licenses/bsd-license.php> BSD License
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
 */

	/**
	 * A utility class housing functions related to databases.
	 * 
	 * @package PASL
	 * @subpackage PASL_DB
	 * @category Database
	 * @static
	 * @author Scott Thundercloud <scott.tc@gmail.com>
	 */
	class PASL_DB_Utility
	{
		/**
		 * Function for parsing DSN strings.
		 * 
		 * 
		 * @param String DSN string supporting:
		 * + DBType://username:password@host/database
		 * + DBType://username@host/database
		 * + DBType://host/database
		 * + DBType://host/
		 * 
		 * @return Array An associative array with the keys:
		 * + DBType
		 * + Host
		 * + Database
		 * + Username
		 * + Password
		 */
		public static function ParseDSN($DSN)
		{
			$Matches = Array();
			
			if(!preg_match("/(^[a-zA-Z]*)\:[\/|\\\]{2}(.*)\:?(.*)\@?(.*)\/(.*)/i", $DSN, $Matches)) return false;
			
			// If username and password or a username exists.
			if(preg_match("/:|@/i", $Matches[2])) 
			{
				$Username = ''; 
				$Password = ''; 
				$Host = '';
				
				$SplitUserPass = preg_split("/:|@/", $Matches[2]);

				$Username = $SplitUserPass[0];

				if(count($SplitUserPass) == 2) $Host = $SplitUserPass[1];
				elseif(count($SplitUserPass) == 3)
				{
					$Password = $SplitUserPass[1];
					$Host = $SplitUserPass[2];
				}
			}
			
			$Array = Array();
			$Array["DBType"] = $Matches[1];
			$Array["Host"] = ($Host) ? $Host : $Matches[2];
			$Array["Database"] = $Matches[5];
			$Array["DSN"] = $Matches[0];
			$Array["Username"] = $Username;
			$Array["Password"] = $Password;
			
			return $Array;
		}
	}
?>