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
 * Base abstract for raw driver implementation of MDB2 common API for MySQL
 *
 * @package PASL_DB
 * @subpackage PASL_DB_Driver
 * @category Database
 * @author Danny Graham <good.midget@gmail.com>
 */

abstract class PASL_DB_Driver_Common
{

	/**
	 * Send a query to the database and return any results
	 * Provides a basic level of MDB2 api compatability.
	 * However, unlike MDB2, we're not accepting all the options
	 * nor are we returning a custom result set. Instead we
	 * return the native result handle.  The driver will
	 * implement further actions agaist the result handle
	 *
	 * @param String $query
	 * @return mixed
	 */
	abstract public function query($query, array $bind=null);

	abstract public function free($result);
	abstract public function fetchOne($result,$colnum);
	abstract public function fetchRow($result);
	abstract public function fetchCol($result,$colnum);
	abstract public function fetchAll($result);

	/**
	 * Username for the database if singleton pattern is called.
	 * 
	 * @var string
	 */
	static public $username = null;
	
	/**
	 * Password for the database if singleton pattern is called.
	 * 
	 * @var string
	 */
	static public $password = null;
	
	/**
	 * Host for the database if singleton pattern is called.
	 * 
	 * @var string
	 */
	static public $host = null;
	
	/**
	 * Database name for the database if singleton pattern is called.
	 * 
	 * @var string
	 */
	static public $database = null;
	
	/**
	 * Execute the specified query, fetch the value from the first column of
	 * the first row of the result set and then frees
	 * the result set.
	 *
	 * @param   String  the SELECT query statement to be executed.
	 * @param   int     the column number to fetch
	 * @return  mixed   field value
	 */
	function queryOne($query,$colnum = 0)
	{
		$result = $this->query($query);
		$one = $this->fetchOne($result,$colnum);
		$this->free($result);
		return $one;
	}

	/**
	 * Execute the specified query, fetch the values from the first
	 * row of the result set into an array and then frees
	 * the result set.
	 *
	 * @param   String  the SELECT query statement to be executed.
	 * @return  Array   data array
	 */
	function queryRow($query)
	{
		$result = $this->query($query);
		$row = $this->fetchRow($result);
		$this->free($result);
		return $row;
	}

	/**
	 * Execute the specified query, fetch the value from the first column of
	 * each row of the result set into an array and then frees the result set.
	 *
	 * @param   String  the SELECT query statement to be executed.
	 * @param   int     the row number to fetch
	 * @return  Array   data array
	 */
	function queryCol($query, $colnum = 0)
	{
		$result = $this->query($query);
		$col = $this->fetchCol($result,$colnum);
		$this->free($result);
		return $col;
	}

	/**
	 * Execute the specified query, fetch all the rows of the result set into
	 * a two dimensional array and then frees the result set.
	 *
	 * @param   String  the SELECT query statement to be executed.
	 * @return  Array   data array
	 */
	function queryAll($query, $bind = null)
	{
		$result = $this->query($query, $bind);
		$all = $this->fetchAll($result);
		$this->free($result);
		return $all;
	}
}

?>