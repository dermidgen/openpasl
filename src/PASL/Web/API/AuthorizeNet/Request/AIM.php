<?
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

namespace PASL\Web\API\AuthorizeNet\Request;

require_once('PASL/Web/API/AuthorizeNet/Item.php');



/**
 * A helper class for an AIM request
 * See the documentation on authorize.net for more information
 * 
 * @category authorize.net
 * @package PASL\Web\API\AuthorizeNet\Request
 * @author Scott Thundercloud <scott.tc@gmail.com>
 */
class AIM
{
	/**
	 * @var array
	 */
	private $Items = array();
	
	/**
	 * Validates the property set
	 * 
	 * @param string $name
	 * @param string $val
	 * @return void
	 */
	public function __set($name, $val)
	{
		switch($name)
		{
			case 'x_login':
			case 'x_tran_key':
			case 'x_type':
			case 'x_amount':
			case 'x_card_num':
			case 'x_exp_date':
			case 'x_trans_id':
			case 'x_auth_code':
			case 'x_relay_response':
			case 'x_delim_data':
			case 'x_line_item':
			case 'x_delim_char':
			case 'x_invoice_num':
			case 'x_test_request':
			case 'x_first_name':
			case 'x_last_name':
			case 'x_company':
			case 'x_address':
			case 'x_zip':
			case 'x_country':
			case 'x_phone':
			case 'x_email':
			case 'x_city':
			case 'x_ship_to_first_name':
			case 'x_ship_to_last_name':
			case 'x_ship_to_city':
			case 'x_ship_to_state':
			case 'x_ship_to_country':
			case 'x_ship_to_address':
			case 'x_ship_to_zip':
			case 'x_ship_to_company':
			case 'x_phone':
			case 'x_cust_id':
			case 'x_customer_ip':
			case 'x_state':
			case 'x_header_email_receipt':
			case 'x_footer_email_receipt':
			case 'x_card_code':
			case 'x_tax':
			case 'x_freight':
			case 'x_tax_exempt':
			case 'x_merchant_email':
			case 'x_product_item':
			case 'x_sale_name':
			case 'product_item':
			case 'sale_name':
			case 'test_field':
			case 'another_test_field':
				$Item = new PASL_Web_API_AuthorizeNet_Item;
				$Item->Name = $name;
				$Item->Value = $val;
				$this->Items[] = $Item;
			break;
			
			default:

			break;
		}
	}
	
	/**
	 * Compiles the data from items and returns a valid NVP string
	 * 
	 * @return string
	 */
	public function __toString()
	{
		$http_q_str = '';
		foreach($this->Items AS $Item)
		{
			$a = array($Item->Name => $Item->Value);
			$http_q_str .= http_build_query($a) . '&';
		}
		$http_q_str = rtrim($http_q_str, '&');
		
		return $http_q_str;
	}
}
?>