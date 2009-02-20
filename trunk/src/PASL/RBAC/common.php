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

require_once('PASL/DB/DB.php');
require_once('PASL/ORM/SimpleTable.php');
require_once('PASL/ORM/ORM.php');

/**
 * This class contains common methods to be extended by RBAC-enabled tables or objects.
 *
 * @author Scott Thundercloud
 */
class PASL_RBAC_common extends PASL_ORM_SimpleTable
{
	protected $groupStack = Array();
	private $groupMembership = Array();

	/**
	 * A part of the bitwise layer.
	 *
	 * @var Array
	 */
	private $__DBGroups = Array();

	/**
	 * A part of the bitwise layer in PHP.
	 *
	 * Creates a bitset out of the object's group memberships.
	 *
	 * @param $action
	 * @param $UserId
	 * @return void
	 */
	private function bitwiseCreateGroupMembership($action, $UserId, $ObjectId=null)
	{
		$tableName = $this->schema['table'];
		$objectId = $ObjectId;

		$DB = $this->db;

		$db_ugroups = $DB->queryAll('SELECT `c_name` FROM `m_group` INNER JOIN (`t_user`, `t_group`) ON (t_user.c_uid = m_group.uid AND t_group.c_uid = m_group.gid) WHERE m_group.uid = '.$UserId.'');

		if(!$objectId) $db_groups = $DB->queryAll('SELECT * FROM t_privilege INNER JOIN t_group ON (t_privilege.c_who = t_group.c_uid) WHERE c_role="group" AND c_related_table="'.$tableName.'" AND c_action="'.$action.'" ORDER BY c_who ASC');
		else
		{
			$db_groups = $DB->queryAll('SELECT * FROM t_privilege INNER JOIN t_group ON (t_privilege.c_who = t_group.c_uid) WHERE c_role="group" AND c_related_table="'.$tableName.'" AND c_action="'.$action.'" AND c_related_uid="'.$objectId.'" ORDER BY c_who ASC');

			if(!$db_groups)
			{
				$db_groups = $DB->queryAll('SELECT `c_group`, `t_group`.`c_name` FROM '.$tableName.' INNER JOIN t_group ON (`t_group`.`c_uid` = `'.$tableName.'`.`c_group`) WHERE `t_user`.`c_uid`="'.$objectId.'"');
			}
		}

		$index = 0;
		$cnt = 0;
		$num = 32;
		$num_bits = 32;

		$i = 0;

		foreach($db_groups AS $db_group)
		{
			if($cnt == $num_bits) // every 32nd loop...
			{
				$index++;
				$num = $num_bits + $num;
				$i = 0;
				$cnt = 0;
			}

			$this->groupStack[$index][$db_group['c_name']] = pow(2, $i);

			$i++;
			$cnt++;
		}

		$i=0;
		foreach($this->groupStack AS $group) // Build the membership pool...
		{
			$this->groupStack[$i]['_ugroups'] = 0;
			foreach($db_ugroups AS $db_ugroup)
			{
				if(isset($this->groupStack[$i][$db_ugroup['c_name']])) $this->groupStack[$i]['_ugroups'] += $this->groupStack[$i][$db_ugroup['c_name']];
			}
			$i++;
		}

		$this->__DBGroups = $db_groups; // Doing this so a query is saved.

	}

	/**
	 * A part of the bitwise layer in PHP.
	 *
	 * Checks to see if a specific group is in the bitset.
	 *
	 * @param $GroupName
	 * @return boolean
	 */
	public function bitwiseCheckGroup($GroupName)
	{
		$value = false;

		foreach($this->groupStack AS $group)
		{
			if(!empty($group[$GroupName]))
			{
				$value = $group['_ugroups'] & $group[$GroupName];
				break;
			}
		}

		return ($value) ? true : false;
	}

	/**
	 * Checks an array set for a specific group
	 *
	 * @param array $UserGroups
	 * @return boolean
	 */
	public function CheckGroups($UserGroups)
	{
		foreach($UserGroups AS $group)
		{
			if($this->bitwiseCheckGroup($group['c_name'])) return true;
		}

		return false;
	}

	/**
	 * Authorizes a user on a specific action
	 *
	 * @param string $action
	 * @param int $UserId
	 * @param int $ObjectId
	 * @return boolean
	 */
	public function AuthorizeUser($action, $UserId, $ObjectId=null)
	{
		$DB = $this->db;
		$this->bitwiseCreateGroupMembership($action, $UserId, $ObjectId);

		$IsInGroup = $this->CheckGroups($this->__DBGroups);

		if($ObjectId)
		{
			if($action == "write" || "delete" || "read")
			{

				$permissions = array(
				   "owner_read"   => 256,
				   "owner_write"  => 128,
				   "owner_delete" => 64,
				   "group_read"   => 32,
				   "group_write"  => 16,
				   "group_delete" => 8,
				   "other_read"   => 4,
				   "other_write"  => 2,
				   "other_delete" => 1
				);

				$result = $DB->queryAll('SELECT `t_group`.`c_name`, `c_owner`, `c_group`, `c_unixperms` FROM `'.$this->schema['table'].'` INNER JOIN `t_group` ON (`t_group`.c_uid = `'.$this->schema['table'].'`.c_group) WHERE `'.$this->schema['table'].'`.`c_uid` = "'.$ObjectId.'"');

				$result[0]['c_unixperms'] =  octdec("0".$result[0]['c_unixperms']);
				$unixpermissions =  (int) decoct($result[0]['c_unixperms']);
				$owner = $result[0]['c_owner'];
				$group = $result[0]['c_group'];
				$groupName = $result[0]['c_name'];

				$can_write
				   =  (( $owner == $UserId ) && ( $unixpermissions & $permissions['owner_write'] ))
				   || (( $this->bitwiseCheckGroup($groupName)) && ( $unixpermissions & $permissions['group_write'] ))
				   || ( $unixpermissions & $permissions['other_write'] );

				$can_read
				   =  (( $owner == $UserId )
				         && ( $unixpermissions & $permissions['owner_read'] ))
				   || (( $this->bitwiseCheckGroup($groupName))
				         && ( $unixpermissions & $permissions['group_read'] ))
				   ||       ( $unixpermissions & $permissions['other_read'] );

				$can_delete
				   =  (( $owner == $UserId )
				         && ( $unixpermissions & $permissions['owner_delete'] ))
				   || (( $this->bitwiseCheckGroup($groupName))
				         && ( $unixpermissions & $permissions['group_delete'] ))
				   ||       ( $unixpermissions & $permissions['other_delete'] );


				switch($action)
				{
					case "read":
						if($can_read) return true;
						else return false;
					break;

					case "write":
						if($can_write) return true;
						else return false;
					break;

					case "delete":
						if($can_delete) return true;
						else return false;
					break;
				}

			}
			else
			{
				$result = $DB->queryAll('SELECT COUNT(`c_role`) AS count FROM `t_privilege` WHERE `c_role` = "user" AND `c_action` = "'.$action.'" AND `c_who` = "'.$UserId.'" AND `c_related_table` = "'.$this->schema['table'].'" AND `c_related_uid` = "'.$ObjectId.'"');
			}
		}
		else
		{
			$result = $DB->queryAll('SELECT COUNT(`c_role`) AS count FROM `t_privilege` WHERE `c_role` = "user" AND `c_action` = "'.$action.'" AND `c_who` = "'.$UserId.'" AND `c_related_table` = "'.$this->schema['table'].'"');
		}

		$result = ($result[0]['count'] > 0) ? true : false;

		if($result || $IsInGroup) return true;
		else return false;
	}
}
?>