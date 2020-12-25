<?php
$prefix = substr(getcwd(), -strlen('/lib/exe')) == '/lib/exe' ? '../../' : '';
if (!defined('WB_CONTROLLER')) define('WB_CONTROLLER', 'doku.php');
if (file_exists("{$prefix}lib/plugins/workbookcore/__dokuinit.php")) {
    include_once("{$prefix}lib/plugins/workbookcore/__dokuinit.php");
} else { // BACKUP-INIT
    include_once(__DIR__ . '/__wbinit.copy.php');
}