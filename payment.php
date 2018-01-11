<?php

$useSSL = true;

require('../../config/config.inc.php');
Tools::displayFileAsDeprecated();

$controller = new FrontController();
$controller->init();

Tools::redirect(Context::getContext()->link->getModuleLink('xchange', 'payment'));