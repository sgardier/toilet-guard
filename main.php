<?php
/**
 * @maj     : 2020.11.05
 * @author  : Simon Gardier
 */

$title = 'Toilet Security Guard';
$description = 'Is your family not respecting you home toilets. Here is the so-lu-tion';

require_once 'php/headerManager.php';
require_once 'php/mainManager.php';
require_once 'php/identificationManager.php';

use Managers\HeaderManager;
use Managers\MainManager;
use Managers\IdentificationManager;

$identity = IdentificationManager::isIdentified();

MainManager::handlingEvents($identity);

echo HeaderManager::gen_header($title, $description);
echo MainManager::gen_main();

