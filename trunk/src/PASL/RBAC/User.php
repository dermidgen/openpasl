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
require_once('PASL/RBAC/common.php');

class PASL_RBAC_User extends PASL_RBAC_common
{
	public $schema = Array(
		'table'		=> 't_user',
		'fields'	=> Array(
			'c_uid', 'c_owner', 'c_group', 'c_unixperms',
			'c_status', 'c_username', 'c_password', 'c_group_memberships'
		),
		'pkeys'		=> Array('c_uid','c_owner','c_group','c_unixperms','c_username','c_group_memberships'),
		'akey'		=> 'c_uid'
	);

	public function __construct($id=null, $username=null)
	{
		$this->db = PASL_ORM::getDB('pasl_rbac');

		if (!is_null($id))
		{
			$this->__loadObject( Array(
				'c_uid' => $id,
				'c_username' => $username
			));
		}
	}

	/**
	 * Checks to see if a user can perform an action on a resource
	 *
	 * @param string $action
	 * @param object $resource
	 * @param int $ObjectId
	 * @return boolean
	 */
	public function can($action, $resource)
	{
		$pkey = $resource->schema['pkeys'][0];
		$ObjectId = $resource->$pkey;

		if($resource->AuthorizeUser($action, $this->c_uid, $ObjectId)) return true;
		else return false;
	}
}
?>