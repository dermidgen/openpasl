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

//		$bind = Array('v_table_name'=>$table,'v_object_id'=>$uid);
//		$res = $db->fetchAll($db->query($query,$bind));
		
		$res = $db->queryAll($query);
		
		return var_export($res, true);
	}
	
	public function getPermissions($user_uid, $resource_uid)
	{
	}
	
	public static function GetInstance()
	{
		if (!self::$instance) self::$instance = new self;
		return self::$instance;
	}
}
?>