<?php
_Wb_::InitSet();
/* -------------------------------------------------------------------- */
class _Wb_ {
    /* -------------------------------------------------------------------- */
    public static $CwdPrefix = ''; // see InitSet
    private static $__DirSearchAr = ['wbinc', 'wbdef', 'wbele', 'wbincdoku', 'wbzdep'];
    private static $__HostInternalList = '.manageopedia.com .manageopedia.net .mphofmann.com ';
    private static $__Prepend = '_wbinit_.prepend.php';
    /* -------------------------------------------------------------------- */
    public static function Autoload(string $inClassNsGroupId): bool {
        if (substr($inClassNsGroupId, 0, 8) != 'workbook') return false;
        if ( ! self::__AutoloadCheck($inClassNsGroupId)) return false;
        $filepath = '';
        foreach (self::__AutoloadPathsAr() as $extpath) {
            $str = WB_ROOT . $extpath . strtr($inClassNsGroupId, ['\\' => '/']) . '.php';
            if (file_exists($str)) {
                $filepath = $str;
                break;
            }
        }
        $p = explode('\\', $inClassNsGroupId);
        $p[0] = 'workbook';
        $p[1] = 'wbzdep';
        $str = WB_ROOT . 'workbook/module/' . strtr(implode('\\', $p), ['\\' => '/']) . '.php';
        if (file_exists($str)) {
            $filepath = $str;
        }
        if ( ! empty($filepath)) {
            include_once($filepath);
            if (method_exists($inClassNsGroupId, 'A_Construct')) {
                $inClassNsGroupId::A_Construct();
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
    public static function ClassExists(string $inClass): bool {
        return class_exists(self::ClassNsGet($inClass) . $inClass);
    }
    /* -------------------------------------------------------------------- */
    public static function ClassMethodExists(string $inClass, string $inMethod): bool {
        return method_exists(self::ClassNsGet($inClass) . $inClass, $inMethod);
    }
    /* -------------------------------------------------------------------- */
    public static function ClassNsGet(string $inClass, array $inDirs = []): string {
        $return = '';
        $inDirs = empty($inDirs) ? self::$__DirSearchAr : $inDirs;
        $pos = strpos($inClass, '\\');
        if ($pos !== false) {
            $classgroup = substr($inClass, 0, $pos);
            $classid = substr($inClass, $pos + 1);
            foreach (self::__AutoloadPathsAr() as $extpath) {
                if ( ! is_dir(WB_ROOT . $extpath)) continue;
                foreach (scandir(WB_ROOT . $extpath) as $ext) {
                    if (substr($ext, 0, 8) != 'workbook') continue;
                    foreach ($inDirs as $dir) {
                        if (file_exists(WB_ROOT . "{$extpath}{$ext}/{$dir}/{$classgroup}/{$classid}.php")) {
                            $return = "{$ext}\\{$dir}\\";
                            break 3;
                        }
                    }
                }
            }
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function ClassNsGroupIdGet(string $inClass): string {
        $class = $inClass;
        $ns = self::ClassNsGet($class);
        if (empty($ns)) {
            $class = strtr($class, ['\\' => '_\\']);
            $ns = self::ClassNsGet($class);
        }
        return (empty($ns)) ? false : $ns . $class;
    }
    /* -------------------------------------------------------------------- */
    public static function CmdExec(string $inCmd, string $inValue = '') {
        $return = '';
        // Paras
        $paras = self::__ParaAr($inCmd);
        if ( ! empty($inValue)) {
            $paras['value'] = $inValue;
            if (strpos($inValue, '=') !== false) {
                $pieces = explode('=', $inValue);
                $paras[$pieces[0]] = $pieces[1];
            }
        }
        // Exec
        $class = $paras['class'];
        $method = $paras['method'];
        $classpathclass = \_Wb_::ClassNsGet($class) . $class;
        if ( ! class_exists($classpathclass)) return self::__ErrorEchoFalse("Error: Class unknown: $inCmd ($class)");
        if ( ! method_exists($classpathclass, $method)) return self::__ErrorEchoFalse("Error: Method unknown: $inCmd ($class::$method)");
        unset($paras['class'], $paras['method']);
        try {
            switch (count($paras)) {
                case 0:
                    $return = $classpathclass::$method();
                    break;
                case 1:
                    $var1 = array_shift($paras);
                    $return = $classpathclass::$method($var1);
                    break;
                case 2:
                    $var1 = array_shift($paras);
                    $var2 = array_shift($paras);
                    $return = $classpathclass::$method($var1, $var2);
                    break;
                case 3:
                    $var1 = array_shift($paras);
                    $var2 = array_shift($paras);
                    $var3 = array_shift($paras);
                    $return = $classpathclass::$method($var1, $var2, $var3);
                    break;
                case 4:
                    $var1 = array_shift($paras);
                    $var2 = array_shift($paras);
                    $var3 = array_shift($paras);
                    $var4 = array_shift($paras);
                    $return = $classpathclass::$method($var1, $var2, $var3, $var4);
                    break;
                case 5:
                    $var1 = array_shift($paras);
                    $var2 = array_shift($paras);
                    $var3 = array_shift($paras);
                    $var4 = array_shift($paras);
                    $var5 = array_shift($paras);
                    $return = $classpathclass::$method($var1, $var2, $var3, $var4, $var5);
                    break;
            }
        } catch (\Throwable $t) {
            return self::__ErrorEchoFalse("ERROR: " . $t->getMessage() . ' (' . $t->getFile() . ' / line: ' . $t->getLine() . ')'); // AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function DebugEcho($inVar0 = '', $inVar1 = '', $inVar2 = '', $inVar3 = '', $inVar4 = '', $inVar5 = '', $inVar6 = '', $inVar7 = '', $inVar8 = '', $inVar9 = ''): void {
        echo self::DebugGet($inVar0, $inVar1, $inVar2, $inVar3, $inVar4, $inVar5, $inVar6, $inVar7, $inVar8, $inVar9);
    }
    /* -------------------------------------------------------------------- */
    public static function DebugGet($inVar0 = '', $inVar1 = '', $inVar2 = '', $inVar3 = '', $inVar4 = '', $inVar5 = '', $inVar6 = '', $inVar7 = '', $inVar8 = '', $inVar9 = ''): string {
        $return = '<pre>';
        foreach ([0, 1, 2, 3, 4, 5, 6, 7, 8, 9] as $i) {
            if ( ! empty(${"inVar$i"})) if (is_array(${"inVar$i"})) $return .= print_r(${"inVar$i"}, true); else $return .= ${"inVar$i"} . "\n";
        }
        $return .= '</pre>';
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function InitSet(): bool {
        self::$CwdPrefix = substr(getcwd(), -strlen('/lib/exe')) == '/lib/exe' ? '../../' : '';
        if (file_exists(self::$CwdPrefix . self::$__Prepend)) include(self::$CwdPrefix . self::$__Prepend);
        $rc = self::__InitConstantSet();
        $rc *= self::__InitPhpSet();
        $rc *= self::__InitDirCheck();
        spl_autoload_register('\_Wb_::Autoload');
        return $rc;
    }
    /* -------------------------------------------------------------------- */
    public static function RunarchCheck(string $inValue): bool {
        return ! (strpos(' ' . WB_RUNARCHLIST . ' ', " $inValue ") === false);
    }
    /* -------------------------------------------------------------------- */
    public static function RunmodeCheck(string $inValue): bool {
        return ! (strpos(' ' . WB_RUNMODELIST . ' ', " $inValue ") === false);
    }
    /* -------------------------------------------------------------------- */
    public static function WbconfAr(string $inType) {
        $returns = [];
        if (is_dir(WB_ROOT . 'workbook/module')) {
            foreach (scandir(WB_ROOT . 'workbook/module') as $module) {
                if (file_exists(WB_ROOT . "workbook/module/$module/wbconf/conf-$inType.ini")) {
                    $inis = parse_ini_file(WB_ROOT . "workbook/module/$module/wbconf/conf-$inType.ini", true);
                    foreach ($inis as $id => $ar) {
                        if (isset($ar['_choices'])) $inis[$id]['_choices'] = explode(',', $ar['_choices']);
                    }
                    $returns = array_merge($returns, $inis);
                }
            }
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    private static function __AutoloadCheck(string $inClassNsGroupId): bool {
        if ((self::RunarchCheck('internal'))) return true;
        if (WB_CONTROLLER == 'wb.php' and strpos($inClassNsGroupId, '\\doku') === false) return true;
        if (substr($inClassNsGroupId, 0, 9) === 'workbook\\') return true;
        return false;
    }
    /* -------------------------------------------------------------------- */
    private static function __AutoloadPathsAr(): array {
        if (self::RunarchCheck('internal')) return ['workbook/module/', 'lib/plugins/'];
        if (WB_CONTROLLER == 'wb.php') return ['workbook/module/'];
        if (substr(WB_CONTROLLER, 0, 4) == 'doku' and self::RunmodeCheck('infra-ioncube')) return ['workbook/moduleplugin/', 'lib/plugins/'];
        if (substr(WB_CONTROLLER, 0, 4) == 'doku') return ['lib/plugins/'];
        return [];
    }
    /* -------------------------------------------------------------------- */
    private static function __ErrorEchoFalse(string $inString): bool {
        if (error_reporting() == 0) return false;
        echo "ERROR: $inString";
        return false;
    }
    /* -------------------------------------------------------------------- */
    private static function __HostInternalCheck() {
        $return = false;
        $list = defined('WB_HOSTINTERNALLIST') ? constant('WB_HOSTINTERNALLIST') : self::$__HostInternalList;
        $ar = explode(' ', $list);
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
    private static function __InitConstantSet(): bool {
        // BASIC
        if ( ! defined('WB_CONTROLLER')) define('WB_CONTROLLER', 'wb.php');
        if ( ! defined('WB_HOSTINTERNALLIST')) define('WB_HOSTINTERNALLIST', self::$__HostInternalList);
        if ( ! defined('WB_ROOT')) define('WB_ROOT', getcwd() . '/' . self::$CwdPrefix);
        // WB_DATA
        if ( ! defined('WB_DATACONF')) define('WB_DATACONF', WB_ROOT . 'conf/');
        if ( ! defined('WB_DATACONFUSER')) define('WB_DATACONFUSER', WB_ROOT . 'conf/users.auth.php');
        if ( ! defined('WB_DATAMEDIA')) define('WB_DATAMEDIA', WB_ROOT . 'data/media/');
        if ( ! defined('WB_DATAMEDIAATTIC')) define('WB_DATAMEDIAATTIC', WB_ROOT . 'data/media_attic/');
        if ( ! defined('WB_DATAMETA')) define('WB_DATAMETA', WB_ROOT . 'data/meta/');
        if ( ! defined('WB_DATAPAGE')) define('WB_DATAPAGE', WB_ROOT . 'data/pages/');
        if ( ! defined('WB_DATAPAGEATTIC')) define('WB_DATAPAGEATTIC', WB_ROOT . 'data/attic/');
        if ( ! defined('WB_DATAWORKBOOK')) define('WB_DATAWORKBOOK', WB_ROOT . 'data/workbook/');
        // WB_DB
        if ( ! defined('WB_DBTYPE')) define('WB_DBTYPE', 'sqlite3');
        if ( ! defined('WB_DBPATH')) define('WB_DBPATH', WB_ROOT . 'data/meta/');
        // WB_PATH
        if ( ! defined('WB_PATHCACHE')) define('WB_PATHCACHE', WB_ROOT . 'workbook/cache/');
        if ( ! defined('WB_PATHLOG')) define('WB_PATHLOG', WB_ROOT . 'workbook/log/');
        if ( ! defined('WB_PATHTMP')) define('WB_PATHTMP', WB_ROOT . 'workbook/tmp/');
        // WB_RUNMODELIST (infra-linux infra-php infra-ioncube infra-webroot module-workbook module-workbookcore template-workbookinternal controller-wb controller-dokuiframe controller-doku host-internal)
        if ( ! defined('WB_RUNMODELIST')) {
            $adds = [];
            if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') $adds[] = 'infra-linux';
            if (version_compare(PHP_VERSION, '7.2', '>=')) $adds[] = 'infra-php';
            if (extension_loaded('ionCube Loader')) $adds[] = 'infra-ioncube';
            if (is_writable('.')) $adds[] = 'infra-webroot';
            if (is_dir(WB_ROOT . "workbook/module/workbook")) $adds[] = 'module-workbook';
            if (is_dir(WB_ROOT . "workbook/module/workbookcore")) $adds[] = 'module-workbookcore';
            if (is_dir(WB_ROOT . "lib/tpl/workbookinternal")) $adds[] = 'template-workbookinternal';
            if (file_exists(WB_ROOT . 'wb.php')) $adds[] = 'controller-wb';
            if (file_exists(WB_ROOT . 'doku.php') and strcmp(substr(@file_get_contents('VERSION'), 0, 10), '2020-07-29') >= 0) {
                if (file_exists(WB_ROOT . 'dokuiframe.php')) $adds[] = 'controller-dokuiframe';
                $adds[] = 'controller-doku';
            }
            if (self::__HostInternalCheck()) $adds[] = 'host-internal';
            define('WB_RUNMODELIST', implode(' ', $adds));
        }
        // WB_RUNARCHLIST (wb internal doku)
        if ( ! defined('WB_RUNARCHLIST')) {
            $adds = [];
            if (\_WB_::RunmodeCheck('controller-wb') and \_WB_::RunmodeCheck('module-workbook') and \_WB_::RunmodeCheck('module-workbookcore')) $adds[] = 'wb';
            if (\_WB_::RunmodeCheck('host-internal') and \_WB_::RunmodeCheck('template-workbookinternal')) $adds[] = 'internal';
            if (\_WB_::RunmodeCheck('controller-doku')) $adds[] = 'doku';
            define('WB_RUNARCHLIST', implode(' ', $adds));
        }
        return true;
    }
    /* -------------------------------------------------------------------- */
    private static function __InitDirCheck(): bool {
        if (strpos(WB_ROOT, '/../') !== false) return true;
        $ar = [ //
            // dirs
            WB_DATAWORKBOOK => '', WB_DATAWORKBOOK . 'sync/' => '', //
            WB_ROOT . 'workbook/' => '', WB_ROOT . 'workbook/cache/' => '', WB_ROOT . 'workbook/module/' => '', WB_ROOT . 'workbook/moduleplugin/' => '', //
            WB_PATHLOG => '', WB_PATHLOG . 'php/' . date('Y-m') => '', WB_PATHLOG . 'user/' . date('Y-m') => '', //
            WB_PATHTMP => '', //
            // links
            WB_ROOT . 'workbook/moduleplugin/workbook' => WB_ROOT . 'workbook/module/workbook', //
        ];
        foreach ($ar as $id => $val) {
            if (file_exists($id)) continue;
            if (empty($val)) {
                mkdir($id, 02700, true);
            } else {
                @symlink($val, $id);
            }
        }
        return true;
    }
    /* -------------------------------------------------------------------- */
    private static function __InitPhpSet(): bool {
        ini_set('log_errors', 1);
        ini_set('error_log', WB_PATHLOG . 'php/' . date('Y-m') . '/error.log');
        ini_set('display_errors', true);
        if (WB_CONTROLLER == 'wb.php') ini_set('error_reporting', E_ALL);
        return true;
    }
    /* -------------------------------------------------------------------- */
    private static function __ParaAr($inString): array {
        $returns = [];
        $pieces = explode(' ', $inString);
        if (strpos($inString, '::') === false) {
            $returns['class'] = 'wbadmincmd_' . @array_shift($pieces);
            $returns['method'] = @array_shift($pieces);
        } else {
            $str = @array_shift($pieces);
            list($returns['class'], $returns['method']) = explode('::', $str);
        }
        foreach ($pieces as $val) {
            $p = explode('=', $val, 2);
            $returns[$p[0]] = $p[1] ?? $p[0];
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
}