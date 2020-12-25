<?php
wb_constantdefine();
spl_autoload_register('wb_autoload');
/* -------------------------------------------------------------------- */
function wb_constantdefine() {
    if (!defined('WB_CONTROLLER')) define('WB_CONTROLLER', 'wb.php');
    if (!defined('WB_INC')) define('WB_INC', (defined('DOKU_INC') ? DOKU_INC : __DIR__ . '/../../../'));
    if (!defined('WB_RUNMODE')) {
        $adds = [];
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') $adds[] = 'infra-ok';
        if (version_compare(PHP_VERSION, '7.2', '>=')) $adds[] = 'php-ok';
        if (extension_loaded('ionCube Loader')) $adds[] = 'ioncube-ok';
        if (strcmp(substr(@file_get_contents('VERSION'), 0, 10), '2020-07-29') >= 0) $adds[] = 'dokuwiki-ok';
        if (is_writable('.')) $adds[] = 'webroot-ok';
        if (is_dir('lib/plugins/workbookcore')) $adds[] = 'workbookcore-ok';
        define('WB_RUNMODE', implode(' ', $adds));
    }
}
/* -------------------------------------------------------------------- */
function wb_autoload($inClassNsGroupId) {
    if (substr($inClassNsGroupId, 0, 8) != 'workbook') return false;
    if (!wb_classallowedcheck($inClassNsGroupId)) return false;
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
function wb_classallowedcheck($inClassNsGroupId) {
    if (!wb_hostallowedcheck()) {
        if (WB_CONTROLLER == 'wb.php' and strpos($inClassNsGroupId, '\doku') !== false) {
            return false;
        } elseif (WB_CONTROLLER == 'doku.php') {
            if (!wb_pluginopencheck($inClassNsGroupId)) return false;
        }
    }
    return true;
}
/* -------------------------------------------------------------------- */
function wb_pluginopencheck($inClassNsGroupId) {
    $plugin = substr($inClassNsGroupId, 0, strpos($inClassNsGroupId, '\\'));
    $ar = ['workbook', 'workbookcore', 'workbookuseracl'];
    return (in_array($plugin, $ar)) ? true : false;
}
/* -------------------------------------------------------------------- */
function wb_hostallowedcheck() {
    $return = false;
    $ar = ['.manageopedia.com', '.manageopedia.net', '.mphofmann.com', 'localhost'];
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
function wb_classnsget($inClass) {
    $return = '';
    $pos = strpos($inClass, '\\');
    if ($pos !== false) {
        $classgroup = substr($inClass, 0, $pos);
        $classid = substr($inClass, $pos + 1);
        foreach (scandir(WB_INC . "lib/plugins/") as $plugin) {
            if (substr($plugin, 0, 8) != 'workbook') continue;
            foreach (['wbtpl', 'wbdef', 'wbtag', 'wbinc',] as $dir) {
                if (file_exists(WB_INC . "lib/plugins/{$plugin}/{$dir}/{$classgroup}/{$classid}.php")) {
                    $return = "{$plugin}\\{$dir}\\";
                    break 2;
                }
            }
        }
    }
    if (!empty($return)) {
        if (!wb_classallowedcheck($return)) $return = '';
    }
    return $return;
}
/* -------------------------------------------------------------------- */