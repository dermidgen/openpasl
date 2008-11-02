<?php

if(!defined('SRCPATH'))
{
	define('SRCPATH', realpath(dirname(__FILE__).'/../../src/PASL'));
	ini_set('include_path', get_include_path().PATH_SEPARATOR . SRCPATH);
}

require_once('simpletest/autorun.php');

require_once 'DB/DBTest.php';
require_once 'Web/Simpl/PageTest.php';

$test = new GroupTest('All Tests');
$test->addTestCase(new PASL_DBTest());
$test->run(new TextReporter());

?>