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
 * Breadcrumb controller using the existing NavMenu classes
 * 
 * @package PASL_Web
 * @subpackage PASL_Web_Simpl
 * @category Web
 * @author Scott Thundercloud <scott.tc@gmail.com>
 *
 */
class Breadcrumb
{
	/**
	 * The template object
	 * 
	 * @var Object
	 * @see \PASL\Web\Template
	 */
	private $Template = null;
	private $Navigation = null;

	public function __construct() { }
	
	public function setNavigation($Navigation)
	{
		$this->Navigation = $Navigation;
	}
	
	/**
	 * Sets the template object for the breadcrumb to use
	 * 
	 * @param $Template
	 * @see \PASL\Web\Template
	 * @return void
	 */
	public function setTemplate($Template)
	{
		$this->Template = $Template;
	}
	
	/**
	 * Gets the template
	 * 
	 * @return unknown_type
	 */
	public function getTemplate()
	{
		return $this->Template;
	}
	
	/**
	 * Sets a hierarchy of breadcrumb items
	 * 
	 * @param array $Hierarchy
	 * @return void
	 */
	public function setHierarchy(array $Hierarchy)
	{
		$this->Hierarchy = $Hierarchy;
	}
	
	/**
	 * Append a navigation menu to the breadcrumb hierarchy
	 * 
	 * @param \PASL\Web\MainNav $Child
	 * @return void
	 */
	public function appendChild($Child)
	{
		$this->Hierarchy[] = $Child;
	}
	
	/**
	 * Append a navigation menu before a menu
	 * 
	 * @param MenuName $Name
	 * @param $Child
	 * @return void
	 */
	public function appendChildBefore($Name, $Child)
	{
		$i = 0;
		$foundIndex = false;
		foreach($this->Hierarchy as $NavMenu)
		{
			if($NavMenu->name == $Name)
			{
				$foundIndex = $i;
				break;
			}
			$i++;
		}
		
		if($foundIndex === false) return false;
		
		for($j=count($this->Hierarchy) - 1; $j >= $foundIndex; $j--)
		{
			$this->Hierarchy[$j + 1] = $this->Hierarchy[$j];  
		}
		
		$this->Hierarchy[$foundIndex] = $Child;
		
		return true;
	}
	
	
	/**
	 * Append a navigation menu after a menu
	 * 
	 * @param MenuName $Name
	 * @param $Child
	 * @return void
	 */
	public function appendChildAfter($Name, $Child)
	{
		$i = 0;
		$foundIndex = false;
		foreach($this->Hierarchy as $NavMenu)
		{
			if($NavMenu->name == $Name)
			{
				$foundIndex = $i;
				break;
			}
			$i++;
		}
		
		if($foundIndex === false) return false;
		$foundIndex++;
		
		for($j=count($this->Hierarchy) - 1; $j >= $foundIndex; $j--)
		{
			$this->Hierarchy[$j + 1] = $this->Hierarchy[$j];  
		}
		
		
		
		$this->Hierarchy[$foundIndex] = $Child;
		
		return true;
	}
	
	/**
	 * Return all the selected children for output
	 * 
	 * @return Array
	 */
	public function getSelectedNavItemsByChildren()
	{
		$Children = array($this->Navigation);
		
		if(!$Children) return;
		
		$foundNavChildren = Array();
		
		$count = count($Children);
		$i=0;
		while(1)
		{
			if($i == $count) break;

			$Child = $Children[$i];
			
			$selectedItem = $Child->getSelectedItem();
			if($selectedItem == null) break;
			$foundNavChildren[] = $Child->getSelectedItem(); 

			if($i == $count - 1)
			{
				$Children = $Child->getChildren();

				if(empty($Children)) break;
				$i=0;
				$count = count($Children);
			}
			else $i++;
		}
		
		return $foundNavChildren;
	}
}


?>