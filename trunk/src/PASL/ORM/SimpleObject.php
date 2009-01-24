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

class PASL_ORM_SimpleObject
{
	protected $table;
	
	public function __construct(PASL_ORM_SimpleTable $table)
	{
		$this->table = $table;
	}
	
	public function __set($name, $value)
	{
		$this->table->$name = $value;
	}
	
	public function __get($name)
	{
		return $this->table->$name;
	}
	
	public function __call($name, $args)
	{
		if (method_exists($this->table, $name)) return call_user_func_array(array($this->table,$name),$args);
		return false;
	}

	public function save()
	{
		//TODO: Add support to get clustered db handle
		$db = Slx_ORM::$db;
		
		$where = $set = $bindwhere = $bindval = Array();
		foreach($this->schema['pkeys'] as $key)
		{
			$set[] = $where[] = "`$key` = ?";
			$bindval[$key] = (isset($this->newValues[$key])) ? $this->newValues[$key] : $this->rowValues[$key] ;
			$bindwhere[] = $this->rowValues[$key];
		}
		$bind = array_merge($bindval,$bindwhere);
		
		//TODO: Map Exception for non AutoInc
		
		$query = "update `{$this->schema['table']}` set ". join(',',$set) ." where ". join(',',$where);
		$db->query($query, $bind);
	}
	
	public function delete()
	{
		//TODO: Add support to get clustered db handle
		$db = Slx_ORM::$db;
		
		$pkey = $this->schema['pkeys'][0];
		$query = "delete from {$this->schema['table']} where `$pkey` = '{$this->$pkey}'";
		
		$db->query($query);
	}
}

?>