<?php

error_reporting(E_ALL);

define('_JEXEC', 1);
define('_PHPUNIT', 1);
define('JPATH_BASE', "/var/www/html/capmex.dev/public_html/");

require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

// Instantiate the application.
$app = JFactory::getApplication('site');
