<?php
/**
 * @package loremipsum
 * @subpackage controllers
 */
require_once dirname(dirname(__FILE__)).'/model/loremipsum/loremipsum.class.php';
$loremipsum = new LoremIpsum($modx);
return $loremipsum->initialize('mgr');