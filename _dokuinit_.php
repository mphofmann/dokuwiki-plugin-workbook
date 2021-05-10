<?php
$cwdprefix = substr(getcwd(), -strlen('/lib/exe')) == '/lib/exe' ? '../../' : '';
if (!defined('WB_CONTROLLER')) define('WB_CONTROLLER', 'doku.php');
if (file_exists("{$cwdprefix}workbook/module/workbookcore/_wbinit_.php")) {
    include_once("{$cwdprefix}workbook/module/workbookcore/_wbinit_.php");
} else { // BACKUP-INIT
    include_once(__DIR__ . '/_wbinit_.copy.php');
}