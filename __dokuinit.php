<?php
$prefix = substr(getcwd(), -strlen('/lib/exe')) == '/lib/exe' ? '../../' : '';
if (file_exists("{$prefix}lib/plugins/workbookcore/__dokuinit.php")) {
    include_once("{$prefix}lib/plugins/workbookcore/__dokuinit.php");
} else { // BACKUP-INIT
    if (!defined('WB_CONTROLLER')) define('WB_CONTROLLER', 'doku.php');
    include_once(__DIR__ . '/__wbinit.backup.php');
}