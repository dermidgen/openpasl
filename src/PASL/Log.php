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
namespace PASL;

class Log
{
	public static $logLevel = 0;
	public static $logFile;

	private static function logToFile($strMessage, $bLineBreaks=true)
	{
		file_put_contents(self::$logFile, ($bLineBreaks) ? sprintf("%s\n",$strMessage) : $strMessage, FILE_APPEND);
	}

	private static function logToScreen($strMessage, $bLineBreaks=true)
	{
		print ($bLineBreaks) ? sprintf("%s\n",$strMessage) : $strMessage;
	}

	public static function setLogFile($strPath)
	{
		self::$logFile = realpath($strPath);
	}

	public static function setLogLevel($intLogLevel)
	{
		self::$logLevel = $intLogLevel;
	}

	public static function Add($strMessage, $bLineBreaks=true)
	{
		if (self::$logLevel === 0) return false;
		if (self::$logLevel >= 1) self::logToFile($strMessage, $bLineBreaks);
		if (self::$logLevel >= 2) self::logToScreen($strMessage, $bLineBreaks);
	}
}
?>