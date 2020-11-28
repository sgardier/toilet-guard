<?php
/**
 * @maj     : 2020.11.05
 * @author  : Simon Gardier
 */

$title = 'Toilet Security Guard';
$description = 'Is your family not respecting you home toilets. Here is the so-lu-tion';

require_once 'php/headerManager.php';
require_once 'php/indexManager.php';
require_once 'php/identificationManager.php';

use Managers\HeaderManager;
use Managers\IndexManager;
use Managers\IdentificationManager;

$identity = IdentificationManager::isIdentified();

echo HeaderManager::gen_header($title, $description);
echo IndexManager::gen_main();
