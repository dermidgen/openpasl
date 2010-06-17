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

namespace PASL\Web\Simpl;

/**
 * NavMenu provides a Menu of NavItems for display in a page template

 * @package PASL_Web
 * @subpackage PASL_Web_Simpl
 * @category Web
 * @author Danny Graham <good.midget@gmail.com>
 */
// TODO: Implement interface for nav items. PASL_Web_Simpl_NavItem

class NavMenu
{
	public $style;
	public $selectedItem = null;
	public $menuItems = Array();
	public $name = null;
	public $children = Array();
	public $parents = Array();
	
	public function __construct($name=null)
	{
		$this->name = $name;
	}

	/**
	 * Sets the selected property of a nav item to true
	 *
	 * @param PASL_Web_Simpl_NavItem $item
	 */
	protected function selectItem($item)
	{
		$this->selectedItem = $item;
		$item->selected = true;
	}

	public function getItemAt($index)
	{
		if (isset($this->menuItems[$index])) return $this->menuItems[$index];
	}

	public function getItemByAttribute($attribute, $value)
	{
		foreach($this->menuItems as $item)
		{
			if (isset($item->$attribute) && $item->$attribute == $value) return $item;
		}
	}

	public function getItemByName($name)
	{
		return $this->getItemByAttribute('title', $name);
	}

	/**
	 * Selects the item at the given index
	 *
	 * @param int $index
	 * @return PASL_Web_Simpl_NavItem
	 */
	public function selectItemAt($index)
	{
		$this->selectItem($this->getItemAt($index));

		return $this->selectedItem;
	}

	/**
	 * Returns a menu item matching the given attribute value
	 *
	 * @param String $attribute
	 * @param String $value
	 *
	 * @return PASL_Web_Simpl_NavItem
	 */
	public function selectItemByAttribute($attribute, $value)
	{
		$this->selectItem($this->getItemByAttribute($attribute, $value));

		return $this->selectedItem;
	}

	/**
	 * Selects a menu item by the provided name
	 *
	 * @param String $name
	 *
	 * @return PASL_Web_Simpl_NavItem
	 */
	public function selectItemByName($name)
	{
		$this->selectItem($this->getItemByName($name));

		return $this->getSelectedItem();
	}

	/**
	 * Returns the selected item
	 *
	 * @return PASL_Web_Simpl_NavItem
	 */
	public function getSelectedItem()
	{
		return $this->selectedItem;
	}

	/**
	 * Adds an item to the menu
	 *
	 * @param PASL_Web_Simpl_NavItem $menuItem
	 */
	public function addMenuItem(\PASL\Web\Simpl\NavItem $menuItem)
	{
		array_push($this->menuItems, $menuItem);
	}

	/**
	 * Adds a header item to the menu
	 *
	 * @param PASL_Web_Simpl_NavItem $header
	 */
	public function addMenuHeader(\PASL\Web\Simpl\NavItem $header)
	{
		array_push($this->menuItems, $header);
	}
	
	public function addChild($Child)
	{
		$this->children[] = $Child;
	}
	
	public function getChildren()
	{
		return $this->children;
	}
	
	public function addParent($Parent)
	{
		$this->parents[] = $Parent;
	}
	
	public function getParents()
	{
		return $this->parents;
	}

	public function display()
	{
		foreach($this->menuItems as $item)
		{
			print $item;
		}
	}

	public function __toString()
	{
		$html = '';
		foreach($this->menuItems as $item)
		{
			$html .= (string) $item;
		}

		return $html;
	}
}

?>