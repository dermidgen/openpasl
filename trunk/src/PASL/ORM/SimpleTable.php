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

require_once('PASL/ORM/SimpleObject.php');

class PASL_ORM_SimpleTable
{
	/**
	 * @var PASL_DB_Driver_mysql
	 */
	public $db;

	/**
	 * @var array
	 */
	public $rowValues = Array();

	public $newValues = Array();

	/**
	 * @var Slx_ORM_SimpleRow
	 */
	protected $rowDecorator;

	/**
	 * @var array
	 */
	public $schema = Array();

	protected function _decorateAsRow()
	{
		$this->rowDecorator = new PASL_ORM_SimpleObject($this);
	}

	public function __call($name, $args)
	{
		if (isset($this->rowDecorator) && method_exists($this->rowDecorator, $name)) return call_user_func_array(array($this->rowDecorator,$name),$args);

		return null;
	}

	public function __set($name, $value)
	{
		if (isset($this->rowValues[$name]))
		{
			if (isset($this->rowDecorator) && $this->rowValues[$name] != $value) $this->newValues[$name] = $value;
			else $this->rowValues[$name] = $value;
		}
		else $this->rowValues[$name] = $value;
	}

	public function __get($name)
	{
		if (isset($this->rowValues[$name]))
		{
			if (isset($this->newValues[$name])) return $this->newValues[$name];
			else return $this->rowValues[$name];
		}
	}

	/**
	 * Get's a db handle
	 *
	 * @param $data
	 * @return PASL_DB_Driver_common
	 */
	protected function getDB(array $data=null)
	{
		if (isset($this->schema['is_clustered']) && $this->schema['is_clustered'] == true) return $this->getClusteredDB($data);
		return $this->db;
	}

	protected function getClusteredDB(array $data=null)
	{
	}

	protected function __stmtprep(array $params, $strict=false)
	{
		$where = $bind = Array();
		foreach($this->schema['pkeys'] as $key)
		{
			if (!$strict && !isset($params[$key])) continue;

			$where[] = "`$key` = ?";
			$bind[$key] = $params[$key];
		}

		return Array('where'=>$where,'bind'=>$bind);
	}

	protected function __loadObject(array $params)
	{
		$db = $this->getDB($params);

		//TODO: Implement :c_key tokens

		$stmtParts = $this->__stmtprep($params);
		$query = "select * from `{$this->schema['table']}` where " . join(' AND ', $stmtParts['where']);

		$res = $db->query($query, $stmtParts['bind']);
		if (!$res) return;
		$res = $db->fetchRow($res);
		if (!$res) return;

		$this->decorate($res);
		$this->_decorateAsRow();
	}

	public function decorate(array $data)
	{
		if (!count($data)) return;
		foreach($data as $key=>$value) $this->$key = $value;
	}

	public function save()
	{
		if ($this->rowDecorator) return $this->rowDecorator->save();

		$db = $this->getDB();

		$bind = $keys = Array();
		foreach($this->rowValues as $key=>$val)
		{
			$keys[] = "`$key`";
			$tokens[] = '?';
			$bind[$key] = $val;
		}

		$query = "insert into `{$this->schema['table']}` (". join(',',$keys) .") VALUES (". join(',',$tokens) .")";
		$db->query($query, $bind);
		$this->_decorateAsRow();
	}

	public function deleteRow(array $params)
	{
		$db = $this->getDB($params);

		$stmtParts = $this->__stmtprep($params);
		$query = "delete from `{$this->schema['table']}` where " . join(' AND ', $stmtParts['where']);
		$db->query($query, $stmtParts['bind']);
	}

	public function removeRecord(array $keys)
	{
		$this->deleteRow($keys);
	}

	public function getData()
	{
		$db = $this->getDB();
		$query = "select * from `{$this->schema['table']}`";
		return $db->queryAll($query);
	}
}

?>