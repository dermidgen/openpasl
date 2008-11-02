<?php

if(!defined('SRCPATH'))
{
	define('SRCPATH', realpath(dirname(__FILE__).'/../../../src/PASL'));
	ini_set('include_path', get_include_path().PATH_SEPARATOR . SRCPATH);
}

require_once('simpletest/autorun.php');
require_once('Web/Simpl/Page.php');

class PASL_Web_Simpl_PageTest extends UnitTestCase
{
	/**
	 * @var PASL_Web_Simpl_Page
	 */
	private $page;

	public function __construct()
	{
		$this->page = new PASL_Web_Simpl_Page();
	}
}

?>