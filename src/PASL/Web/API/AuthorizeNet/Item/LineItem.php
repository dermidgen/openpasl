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

namespace PASL\Web\API\AuthorizeNet\Item;

/**
 * Helper class to create a line item
 * Refer to the authorize.net documentation for more information
 * 
 * @category authorize.net
 * @package PASL\Web\API\AuthorizeNet\Item
 * @author Scott Thundercloud <scott.tc@gmail.com>
 */
class LineItem
{
	/**
	 * @var string
	 */
	private $ID;
	

	/**
	 * @var string
	 */
	private $Name;
	

	/**
	 * @var string
	 */
	private $Description;
	

	/**
	 * @var string
	 */
	private $Quantity;
	

	/**
	 * @var string
	 */
	private $Price;
	

	/**
	 * @var string
	 */
	private $Taxable;
	
	/**
	 * Accepts params for line items
	 * 
	 * @param string $ID
	 * @param string $Name
	 * @param string $Description
	 * @param string $Quantity
	 * @param string $Price
	 * @param string $Taxable
	 * @return void
	 */
	public function __construct($ID=null, $Name=null, $Description=null, $Quantity=null, $Price=null, $Taxable=null)
	{
		$this->setID($ID);
		$this->setName($Name);
		$this->setDescription($Description);
		$this->setQuantity($Quantity);
		$this->setPrice($Price);
		$this->setTaxable($Taxable);
	}

	/**
	 * Set the ID
	 * 
	 * @param string $ID
	 * @return void
	 */
	public function setID($ID)
	{
		$this->ID = $ID;
	}
	
	/**
	 * Set the name
	 * 
	 * @param string $Name
	 * @return void
	 */
	public function setName($Name)
	{
		$this->Name = $Name;
	}
	
	/**
	 * Set the description
	 * 
	 * @param string $Description
	 * @return void
	 */
	public function setDescription($Description)
	{
		$this->Description = $Description;
	}

	/**
	 * Set the quantity
	 * 
	 * @param string $Quantity
	 * @return void
	 */
	public function setQuantity($Quantity)
	{
		$this->Quantity = $Quantity;
	}
	
	/**
	 * Set the price
	 * 
	 * @param string $Price
	 * @return void
	 */
	public function setPrice($Price)
	{
		$this->Price = $Price;
	}
	
	/**
	 * Sets if it's taxable
	 * 
	 * @param string $Taxable
	 * @return void
	 */
	public function setTaxable($Taxable)
	{
		$this->Taxable = $Taxable;
	}
	
	/**
	 * Compiles properties into a line item
	 * 
	 * @return string
	 */
	public function __toString()
	{
		$q_string = sprintf('%s<|>%s<|>%s<|>%s<|>%s<|>%s', $this->ID, $this->Name, $this->Description, $this->Quantity, $this->Price, $this->Taxable);
		return $q_string;
	}
}


 ?>