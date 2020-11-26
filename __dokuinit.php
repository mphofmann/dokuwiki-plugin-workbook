<?php
/* -------------------------------------------------------------------- */
// License MIT
/* -------------------------------------------------------------------- */
__workbookconstantdefine();
spl_autoload_register('__workbookautoload');
if (file_exists('/opt/sapp/sapp-common/bin/__sappinitlib.inc')) {
    include_once('/opt/sapp/sapp-common/bin/__sappinitlib.inc');
    __sappconstantdefine();
    spl_autoload_register('__sappautoload');
}
/* -------------------------------------------------------------------- */
function __workbookconstantdefine() {
    if (!defined('WB_INC')) {
        $const = (defined('DOKU_INC')) ? constant('DOKU_INC') : __DIR__ . '/../../../';
        define('WB_INC', $const);
    }
    if (!defined('WB_RUNMODE')) {
        $adds = [];
        $adds[] = 'doku.php';
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
    if (substr($inClassNsGroupId, 0, 8) != 'workbook') {
        return false;
    }
    $filepath = WB_INC . "lib/plugins/" . strtr($inClassNsGroupId, ['\\' => '/']) . '.php';
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
function __workbookclassnsget($inClass) {
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