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
 * DB is more of a utility than an abstraction layer.  DB is designed to
 * allow the implementation of some basic concepts from PEAR::MDB2 using
 * PHP's raw driver support.  While some of the features of MDB2 are
 * useful, the overhead is totally unecessary.  However, in the event
 * that you need to write portable queries to a datasource that may be
 * subject to change, DB provides access to MDB2.  Use of raw drivers is
 * encouraged at the expense of writing totally portable queries.  In an
 * effort to reduce refactoring, should certain aspects of application
 * require switching from a raw driver to MDB2, we try to match some of
 * the common API calls from MDB2.  This allows a simple switch of the
 * DB driver instance to an MDB2 instance through DB::portable().

 * @package PASL
 * @subpackage PASL_DB
 * @category Database
 * @author Danny Graham <good.midget@gmail.com>
 */
class PASL_DB
{
	/**
	 * Database connector
	 * @access public
	 * @static
	 * @var MDB2_Driver_mysql
	 */
	public static $mdb2 = null;

	/**
	 * An array of instantiated raw drivers
	 *
	 * @access private
	 * @static
	 * @var array
	 */
	private static $drivers = Array();

	//TODO: Refactor to support mdb2 style dsn for raw driver connections
	//TODO: Refactor to implement factories for raw drivers and mdb2 connections

	/**
	 * Intializes and connects an instance of MDB2
	 *
	 * @return MDB2_Driver_mysql
	 */
	public static function connect()
	{
		include_once("MDB2.php");
		//TODO: implement factory for db objects
		if (!is_null(PASL_DB::$mdb2)) return PASL_DB::$mdb2;

		PASL_DB::$mdb2 = MDB2::singleton();
		if (PEAR::isError(PASL_DB::$mdb2)) die(PASL_DB::$mdb2->getMessage());

		return PASL_DB::$mdb2;
	}
}

?>