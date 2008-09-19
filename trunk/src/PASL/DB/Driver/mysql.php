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

include_once("Common.php");

/**
 * Provides RAW driver implementation of MDB2 common API for MySQL
 *
 * @package PASL_DB
 * @subpackage PASL_DB_Driver
 * @category Database
 * @author Danny Graham <good.midget@gmail.com>
 */
class PASL_DB_Driver_mysql extends PASL_DB_Driver_Common
{
	//TODO: Implement abstract methods from DB_Driver_Common
	//TODO: Implement raw driver support for queries
	//TODO: Implement raw driver connections via mdb2 style dsn

	/**
	* @var PASL_DB_Driver_mysql
	*/
	private static $instance = null;

	public function __construct()
	{

	}

	public function query($query)
	{
	}

	public function free($result)
	{
	}

	public function fetchOne($result,$colnum)
	{
	}

	public function fetchRow($result)
	{
	}

	public function fetchCol($result,$colnum)
	{
	}

	public function fetchAll($result)
	{
	}

	/**
	 * Returns an instance of PASL_DB_Driver_mysql (singleton)
	 *
	 * @return PASL_DB_Driver_mysql
	 */
	public static function GetInstance()
	{
		if (PASL_DB_Driver_mysql::$instance == null) PASL_DB_Driver_mysql::$instance = new PASL_DB_Driver_mysql();
		return PASL_DB_Driver_mysql::$instance;
	}
}

?>