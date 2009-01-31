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

require_once('PASL/ORM/ORM.php');

require_once('PASL/RBAC/Action.php');

class PASL_RBAC
{
	private static $instance;

	public function __construct()
	{

	}

	public function init($dsn)
	{
		PASL_ORM::registerDB('pasl_rbac', PASL_DB::factory($dsn));
	}

	public function getDB()
	{
		return PASL_ORM::getDB('pasl_rbac');
	}

	public function getObjectACLs($type, $uid)
	{
		$db = $this->getDB();

		$query = "select
			    pr.c_role,
			    pr.c_who,
			    case
			        when (pr.c_role = 'user') then coalesce(us.c_username, '--DNE--')
			        when (pr.c_role = 'group') then ''
			        when (pr.c_role = 'owner_group') then ''
			        else 'none'
			    end as c_name,
			    pr.c_action,
			    pr.c_type,
			    pr.c_related_table as c_table,
			    pr.c_related_uid,
			    ia.c_status
			from t_privilege as pr
			    inner join t_action as ac on ac.c_title = pr.c_action
			    inner join %s as ob on ob.c_uid = %s
			    inner join t_implemented_action as ia on ia.c_table = '%s'
			        and ia.c_action = ac.c_title
			    left outer join t_user as us
			        on pr.c_role = 'user'
			        and pr.c_who = us.c_uid
			where (
			        (pr.c_type = 'object' and pr.c_related_uid = %s)
			        or (pr.c_type in ('table', 'global'))
			        or (pr.c_role = 'self' and pr.c_related_table = 't_user'))
			    and pr.c_related_table = '%s';";

		$query = sprintf($query, $type, $uid, $type, $uid, $type);
		$res = $db->queryAll($query);

		//TODO: Once these queries get locked in we'll want them to be stored procedures
		// We'll also want to make sure that in any case where unqualified user input may be passed
		// we're using prepared statements to ensure data is clean.

//		$query = 'call allACLEntries(?,?)';
//		$bind = Array('v_table_name'=>$type,'v_object_id'=>$uid);
//		$res = $db->fetchAll($db->query($query,$bind));

		return '<pre>' . var_export($res, true) . '</pre>';
	}

	public function getAllTablePrivileges($table, $user_id, $user_groups, $groups)
	{
		$DB = $this->getDB();

		$query = "
		select ac.c_title
		from
		    t_action as ac
		    -- Privileges that apply to the table and grant the given action
		    -- Not an inner join because the action may be granted even if there is no
		    -- privilege granting it.  For example, root users can take all actions.
		    left outer join t_privilege as pr
		        on pr.c_related_table = '%s'
		            and pr.c_action = ac.c_title
		            and pr.c_type = 'table'
		where
		    -- The action must apply to tables (NOT apply to objects)
		    (ac.c_apply_object = 0) and (
		        -- Members of the 'root' group are always allowed to do everything
		        (%s & %s <> 0)
		        -- user privileges
		        or (pr.c_role = 'user' and pr.c_who = %s)
		        -- group privileges
		        or (pr.c_role = 'group' and (pr.c_who & %s <> 0)))
		";

		$query = sprintf($query, $table, $user_groups, $groups['root'], $user_id, $user_groups);

		$res = $db->queryAll($query);

		return var_export($res, true);
	}

	public function getAllActionableObjects($tbl, $action, $user_id, $user_groups, $groups, $permissions)
	{
		$DB = $this->getDB();

		$query = "
	select distinct obj.*
	from $tbl as obj
	   -- Filter out actions that do not apply to this object type
	   inner join t_implemented_action as ia
	      on ia.c_table = '$tbl'
	         and ia.c_action = '$action'
	         and ((ia.c_status = 0) or (ia.c_status & obj.c_status <> 0))
	   inner join t_action as ac
	      on ac.c_title = '$action'
	   -- Privileges that apply to the object (or every object in the table)
	   -- and grant the given action
	   left outer join t_privilege as pr
	      on pr.c_related_table = '$tbl'
	         and pr.c_action = '$action'
	         and (
	            (pr.c_type = 'object' and pr.c_related_uid = obj.c_uid)
	            or pr.c_type = 'global'
	            or (pr.c_role = 'self' and $user_id = obj.c_uid and '$tbl' = 't_user'))
	where
	   -- The action must apply to objects
	   ac.c_apply_object
	   and (
	      -- Members of the 'root' group are always allowed to do everything
	      ($user_groups & $groups[root] <> 0)
	      -- UNIX style read permissions in the bit field
	      or (ac.c_title = 'read' and (
	         -- The other_read permission bit is on
	         (obj.c_unixperms & $permissions[other_read] <> 0)
	         -- The owner_read permission bit is on, and the member is the owner
	         or ((obj.c_unixperms & $permissions[owner_read] <> 0)
	            and obj.c_owner = $user_id)
	         -- The group_read permission bit is on, and the member is in the group
	         or ((obj.c_unixperms & $permissions[group_read] <> 0)
	            and ($user_groups & obj.c_group <> 0))))
	      -- UNIX style write permissions in the bit field
	      or (ac.c_title = 'write' and (
	         -- The other_write permission bit is on
	         (obj.c_unixperms & $permissions[other_write] <> 0)
	         -- The owner_write permission bit is on, and the member is the owner
	         or ((obj.c_unixperms & $permissions[owner_write] <> 0)
	            and obj.c_owner = $user_id)
	         -- The group_write permission bit is on, and the member is in the group
	         or ((obj.c_unixperms & $permissions[group_write] <> 0)
	            and ($user_groups & obj.c_group <> 0))))
	      -- UNIX style delete permissions in the bit field
	      or (ac.c_title = 'delete' and (
	         -- The other_delete permission bit is on
	         (obj.c_unixperms & $permissions[other_delete] <> 0)
	         -- The owner_delete permission bit is on, and the member is the owner
	         or ((obj.c_unixperms & $permissions[owner_delete] <> 0)
	            and obj.c_owner = $user_id)
	         -- The group_delete permission bit is on, and the member is in the group
	         or ((obj.c_unixperms & $permissions[group_delete] <> 0)
	            and ($user_groups & obj.c_group <> 0))))
	      -- user privileges
	      or (pr.c_role = 'user' and pr.c_who = $user_id)
	      -- owner privileges
	      or (pr.c_role = 'owner' and obj.c_owner = $user_id)
	      -- owner_group privileges
	      or (pr.c_role = 'owner_group' and (obj.c_group & $user_groups <> 0))
	      -- group privileges
	      or (pr.c_role = 'group' and (pr.c_who & $user_groups <> 0)))
	      -- self privileges
	      or pr.c_role = 'self';
			";

		$res = $DB->queryAll($query);

		return var_dump($res, true);
	}

	public function getPermissions($user_uid, $resource_uid)
	{
		$DB = $this->getDB();

		$query = "SELECT * FROM t_resource WHERE c_uid = '".$user_uid."'";

		$res = $DB->queryAll($query);
	}

	public static function GetInstance()
	{
		if (!self::$instance) self::$instance = new self;
		return self::$instance;
	}
}
?>