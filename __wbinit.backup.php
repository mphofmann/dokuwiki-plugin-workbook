<?php
__workbookconstantdefine();
spl_autoload_register('__workbookautoload');
/* -------------------------------------------------------------------- */
function __workbookconstantdefine() {
    if (!defined('WB_CONTROLLER')) define('WB_CONTROLLER', 'wb.php');
    if (!defined('WB_INC')) define('WB_INC', (defined('DOKU_INC') ? DOKU_INC : __DIR__ . '/../../../'));
    if (!defined('WB_RUNMODE')) {
        $adds = [];
        if (version_compare(PHP_VERSION, '7.2', '>=')) $adds[] = 'php-ok';
        if (extension_loaded('ionCube Loader')) $adds[] = 'ioncube-ok';
        if (strcmp(substr(@file_get_contents('VERSION'), 0, 10), '2020-07-29') >= 0) $adds[] = 'dokuwiki-ok';
        if (is_writable('.')) $adds[] = 'webroot-ok';
        if (is_dir('lib/plugins/workbookcore')) $adds[] = 'workbookcore-ok';
        define('WB_RUNMODE', implode(' ', $adds));
    }
}
/* -------------------------------------------------------------------- */
function __workbookautoload($inClassNsGroupId) {
    if (substr($inClassNsGroupId, 0, 8) != 'workbook') return false;
    if (WB_CONTROLLER == 'wb.php' and strpos($inClassNsGroupId, '\doku') !== false) {
        if (!__workbookhostallowedcheck()) return false;
    }
    $filepath = WB_INC . 'lib/plugins/' . strtr($inClassNsGroupId, ['\\' => '/']) . '.php';
    if (file_exists($filepath)) {
        include_once($filepath);
        if (method_exists($inClassNsGroupId, '__constructStatic')) {
            $inClassNsGroupId::__constructStatic();
        }
        return true;
    }
    // $ar = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
    // echo "<pre>";
    // echo "ERROR: Class not found: $filepath\n";
    // print_r($ar[2]);
    // echo "</pre>";
    return false;
}
/* -------------------------------------------------------------------- */
function __workbookhostallowedcheck() {
    $return = false;
    $ar = ['.manageopedia.com', '.mphofmann.com', 'localhost',];
    $server = empty($_SERVER['HTTP_HOST']) ? @$_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
    foreach ($ar as $val) {
        if (substr($server, -strlen($val)) == $val) {
            $return = true;
            break;
        }
    }
    return $return;
}
/* -------------------------------------------------------------------- */
function workbookclassnsget($inClass) {
    $return = false;
    $pos = strpos($inClass, '\\');
    if ($pos !== false) {
        $classgroup = substr($inClass, 0, $pos);
        $classid = substr($inClass, $pos + 1);
        foreach (scandir(WB_INC . "lib/plugins/") as $plugin) {
            if (substr($plugin, 0, 8) != 'workbook') continue;
            foreach (['wbtpl', 'wbdef', 'wbtag', 'wbinc',] as $dir) {
                if (file_exists(WB_INC . "lib/plugins/{$plugin}/{$dir}/{$classgroup}/{$classid}.php")) {
                    return "{$plugin}\\{$dir}\\";
                }
            }
        }
    }
    return $return;
}
/* -------------------------------------------------------------------- */