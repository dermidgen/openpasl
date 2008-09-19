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
	//TODO: implement mdb2 style dsn parsing
	//TODO: add abstract methods for basic querying mdb2 style
	//TODO: implement common driver methods following mdb2 style (queryAll,queryOne,etc)

	/**
	 * @see PASL_DB_Utility
	 */
	public function ParseDSN($DSN)
	{
		$DSNParsed = PASL_DB_Utility::ParseDSN($DSN);
		return $DSNParsed;
	}

	 /**
     * Send a query to the database and return any results
     *
     * @param   string  the SQL query
     * @param   mixed   array that contains the types of the columns in
     *                        the result set
     * @param   mixed   string which specifies which result class to use
     * @param   mixed   string which specifies which class to wrap results in
     *
     * @return mixed   an MDB2_Result handle on success, a MDB2 error on failure
     *
     * @access  public
     */
    public function query($query, $types = null, $result_class = true, $result_wrap_class = false)
    {

    }

        /**
     * Execute the specified query, fetch the value from the first column of
     * the first row of the result set and then frees
     * the result set.
     *
     * @param   string  the SELECT query statement to be executed.
     * @param   string  optional argument that specifies the expected
     *       datatype of the result set field, so that an eventual conversion
     *       may be performed. The default datatype is text, meaning that no
     *       conversion is performed
     * @param   int     the column number to fetch
     *
     * @return  mixed   MDB2_OK or field value on success, a MDB2 error on failure
     *
     * @access  public
     */
    function queryOne($query, $type = null, $colnum = 0)
    {
        $result = $this->query($query, $type);
        if (!MDB2::isResultCommon($result)) {
            return $result;
        }

        $one = $result->fetchOne($colnum);
        $result->free();
        return $one;
    }

    // }}}
    // {{{ function queryRow($query, $types = null, $fetchmode = MDB2_FETCHMODE_DEFAULT)

    /**
     * Execute the specified query, fetch the values from the first
     * row of the result set into an array and then frees
     * the result set.
     *
     * @param   string  the SELECT query statement to be executed.
     * @param   array   optional array argument that specifies a list of
     *       expected datatypes of the result set columns, so that the eventual
     *       conversions may be performed. The default list of datatypes is
     *       empty, meaning that no conversion is performed.
     * @param   int     how the array data should be indexed
     *
     * @return  mixed   MDB2_OK or data array on success, a MDB2 error on failure
     *
     * @access  public
     */
    function queryRow($query, $types = null, $fetchmode = MDB2_FETCHMODE_DEFAULT)
    {
        $result = $this->query($query, $types);
        if (!MDB2::isResultCommon($result)) {
            return $result;
        }

        $row = $result->fetchRow($fetchmode);
        $result->free();
        return $row;
    }

    // }}}
    // {{{ function queryCol($query, $type = null, $colnum = 0)

    /**
     * Execute the specified query, fetch the value from the first column of
     * each row of the result set into an array and then frees the result set.
     *
     * @param   string  the SELECT query statement to be executed.
     * @param   string  optional argument that specifies the expected
     *       datatype of the result set field, so that an eventual conversion
     *       may be performed. The default datatype is text, meaning that no
     *       conversion is performed
     * @param   int     the row number to fetch
     *
     * @return  mixed   MDB2_OK or data array on success, a MDB2 error on failure
     *
     * @access  public
     */
    function queryCol($query, $type = null, $colnum = 0)
    {
        $result = $this->query($query, $type);
        if (!MDB2::isResultCommon($result)) {
            return $result;
        }

        $col = $result->fetchCol($colnum);
        $result->free();
        return $col;
    }

    // }}}
    // {{{ function queryAll($query, $types = null, $fetchmode = MDB2_FETCHMODE_DEFAULT, $rekey = false, $force_array = false, $group = false)

    /**
     * Execute the specified query, fetch all the rows of the result set into
     * a two dimensional array and then frees the result set.
     *
     * @param   string  the SELECT query statement to be executed.
     * @param   array   optional array argument that specifies a list of
     *       expected datatypes of the result set columns, so that the eventual
     *       conversions may be performed. The default list of datatypes is
     *       empty, meaning that no conversion is performed.
     * @param   int     how the array data should be indexed
     * @param   bool    if set to true, the $all will have the first
     *       column as its first dimension
     * @param   bool    used only when the query returns exactly
     *       two columns. If true, the values of the returned array will be
     *       one-element arrays instead of scalars.
     * @param   bool    if true, the values of the returned array is
     *       wrapped in another array.  If the same key value (in the first
     *       column) repeats itself, the values will be appended to this array
     *       instead of overwriting the existing values.
     *
     * @return  mixed   MDB2_OK or data array on success, a MDB2 error on failure
     *
     * @access  public
     */
    function queryAll($query, $types = null, $fetchmode = MDB2_FETCHMODE_DEFAULT,
        $rekey = false, $force_array = false, $group = false)
    {
        $result = $this->query($query, $types);
        if (!MDB2::isResultCommon($result)) {
            return $result;
        }

        $all = $result->fetchAll($fetchmode, $rekey, $force_array, $group);
        $result->free();
        return $all;
    }

}

?>