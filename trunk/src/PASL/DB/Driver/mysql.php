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
 * Provides RAW driver implementation of MDB2 common API for MySQL.
 * By default, all fetch methods return associative arrays which is
 * equal to the MDB2_FETCHMODE_ASSOC flag in MDB2.
 *
 * @package PASL_DB
 * @subpackage PASL_DB_Driver
 * @category Database
 * @author Danny Graham <good.midget@gmail.com>
 */

// TODO: Error checking

class PASL_DB_Driver_mysql extends PASL_DB_Driver_Common
{
	//TODO: Implement abstract methods from DB_Driver_Common
	//TODO: Implement raw driver support for queries
	//TODO: Implement raw driver connections via mdb2 style dsn

	/**
	* @var PASL_DB_Driver_mysql
	*/
	private static $instance = null;

	public $db = null;

	private $lastbind;

	/**
	 * Connects to a MySQL database
	 *
	 * @param string $Host
	 * @param string $Username
	 * @param string $Password
	 * @param string $Database
	 */
	public function __construct($Host, $Username, $Password, $Database)
	{
		$this->db = mysqli_connect($Host, $Username, $Password, $Database);
	}

	private function statementFetchRow($statement)
	{
		if(!$statement->bind) return false;
		
		$statement->fetch();
		
		$row = Array();
		while(list($key, $val) = each($statement->bind)) $row[$key] = $val;

		return $row;
	}

	private function getDataTypes(array $data)
	{
		$types = Array();
		foreach($data as $val)
		{
			switch(gettype($val))
			{
				case 'string':
					$type = 's';
				break;
				case 'integer':
					$type = 'i';
				break;
				case 'double':
					$type = 'd';
				break;
				case 'blob':
					$type = 'b';
				break;
				default:
					$type = 's';
			}

			$types[] = $type;
		}

		return join('',$types);
	}

	/**
	 * Query a MySQL database
	 *
	 * @param string Query
	 * @return MySQLResult
	 */
	public function query($query, array $bind=null)
	{
		if ($bind) // We'll use a prepared statement
		{
			//TODO: Add token replacement for associative keys in query string
			// query string should support 'select * from table where `c_key` = :c_key'
			// should be replaced as 'select * from table where `c_key` = ?'
			$statement = $this->db->prepare($query);
			array_unshift($bind, $this->getDataTypes($bind));
			
			if (!$statement) return $statement;

			$result = call_user_func_array(array($statement,'bind_param'), $bind);

			@$statement->execute();
			$statement->store_result();

			$bind = Array();
			
			if ($statement->num_rows())
			{
				$fields = $statement->result_metadata()->fetch_fields();

				foreach($fields as $field)
				{
					$bind[] = &$row[$field->name];
				}

				@$statement->bind = $row;
				call_user_func_array(array($statement,'bind_result'),$bind);
			}



			return $statement;
		}

		return $this->db->query($query);
	}

	/**
	 * Free a MySQL result
	 *
	 * @param MySQLResult
	 */
	public function free($result)
	{
		$result->close();
	}


    /**
     * Fetch a single column from a result
     *
     * @param MySQLResult The query result
     * @param int|String Column identifier
     * @return mixed
     */
	public function fetchOne($result, $column)
	{
		// TODO: Type checking on colnum
		$fetchMode = (!is_int($column)) ? MYSQL_ASSOC : MYSQL_NUM;
		$One = mysqli_fetch_array($result, $fetchMode);
		$One = $One[$column];
		return $One;
	}

	/**
	 * Fetch the first row of a result
	 *
	 * @param MySQLResult
	 * @return array
	 */
	public function fetchRow($result)
	{
		if (get_class($result) == 'mysqli_stmt') return $this->statementFetchRow($result);
		return mysqli_fetch_assoc($result);
	}

	/**
	 * Fetch a single column from a result
	 *
	 * @param MySQLResult MySQL query result
	 * @param int|String Column identifier
	 * @return array
	 */
	public function fetchCol($result, $column)
	{
		// TODO: Type checking on colnum
		$AssocNew = Array();

		$fetchMode = (!is_int($column)) ? MYSQL_ASSOC : MYSQL_NUM;
		while($AssocArray = mysqli_fetch_array($result, $fetchMode))
		{
			$AssocNew[] = $AssocArray[$column];
		}

		return $AssocNew;
	}

	/**
	 * Fetch a whole result
	 *
	 * @param MySQLResult MySQL query result
	 * @return array
	 */
	public function fetchAll($result)
	{
		$AssocNew = Array();
		
		while($AssocArray = (get_class($result) == 'mysqli_stmt') ? $this->statementFetchRow($result) : mysqli_fetch_array($result, MYSQL_ASSOC))
		{
			$AssocNew[] = $AssocArray;
		}

		return $AssocNew;
	}

	/**
	 * Returns an instance of PASL_DB_Driver_mysql (singleton)
	 *
	 * @return PASL_DB_Driver_mysql
	 */
	public static function GetInstance()
	{
		if (PASL_DB_Driver_mysql::$instance == null) PASL_DB_Driver_mysql::$instance = new PASL_DB_Driver_mysql(self::$host, self::$username, self::$password, self::$database);
		return PASL_DB_Driver_mysql::$instance;
	}
}

?>