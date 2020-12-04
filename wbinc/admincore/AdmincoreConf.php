<?php
namespace workbook\wbinc\admincore;
use workbook\wbinc\admin;
use workbookcore\wbinc\base;
use workbookcore\wbinc\sys;
class AdmincoreConf {
    /* -------------------------------------------------------------------- */
    public static function Confs($inAction) {
        $out = self::__InisCheckGet('confs');
        if (!empty($out)) {
            $status = 'green';
            if (stripos($out, '[notice]') !== false) $status = 'yellow';
            if (stripos($out, '[warning]') !== false) $status = 'orange';
            if (stripos($out, '[error]') !== false) $status = 'red';
            switch ($inAction) {
                case 'status':
                    echo admin\AdminXhtml::StatusGet($status, 'Confs');
                    break;
                case 'check':
                    echo "<pre>$out</pre>";
                    break;
                default:
                    admin\AdminXhtmlMsg::Echo('Warning', __METHOD__, $inAction, 'Parameter unknown.');
                    break;
            }
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Plugins($inAction) {
        $out = self::__InisCheckGet('plugins');
        if (!empty($out)) {
            $status = 'green';
            if (stripos($out, '[notice]') !== false) $status = 'yellow';
            if (stripos($out, '[warning]') !== false) $status = 'orange';
            if (stripos($out, '[error]') !== false) $status = 'red';
            switch ($inAction) {
                case 'status':
                    echo admin\AdminXhtml::StatusGet($status, 'Plugins');
                    break;
                case 'check':
                    echo "<pre>$out</pre>";
                    break;
                default:
                    admin\AdminXhtmlMsg::Echo('Warning', __METHOD__, $inAction, 'Parameter unknown.');
                    break;
            }
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Tpls($inAction) {
        $out = self::__InisCheckGet('tpls');
        if (!empty($out)) {
            $status = 'green';
            if (stripos($out, '[notice]') !== false) $status = 'yellow';
            if (stripos($out, '[warning]') !== false) $status = 'orange';
            if (stripos($out, '[error]') !== false) $status = 'red';
            switch ($inAction) {
                case 'status':
                    echo admin\AdminXhtml::StatusGet($status, 'Templates');
                    break;
                case 'check':
                    echo "<pre>$out</pre>";
                    break;
                default:
                    admin\AdminXhtmlMsg::Echo('Warning', __METHOD__, $inAction, 'Parameter unknown.');
                    break;
            }
        }
    }
    /* -------------------------------------------------------------------- */
    public static function LocalProtected($inAction) {
        $cnew = sys\SysNsid::ContentsGet("zsync:sync:" . sys\SysRemote::VersionGet() . ":conf:local.protected.php", '');
        admin\AdminInode::FileAction($inAction, 'conf/local.protected.php', $cnew);
    }
    /* -------------------------------------------------------------------- */
    public static function Acl($inAction) {
        $cnew = sys\SysNsid::ContentsGet("zsync:sync:" . sys\SysRemote::VersionGet() . ":conf:acl.auth.php", '');
        admin\AdminInode::FileAction($inAction, 'conf/acl.auth.php', $cnew);
    }
    /* -------------------------------------------------------------------- */
    public static function AclWb($inAction) {
        $cnew = sys\SysNsid::ContentsGet("zsync:sync:" . sys\SysRemote::VersionGet() . ":conf:acl.auth.wb.php", '');
        admin\AdminInode::FileAction($inAction, 'conf/acl.auth.wb.php', $cnew);
    }
    /* -------------------------------------------------------------------- */
    public static function Entities($inAction) {
        $cnew = sys\SysNsid::ContentsGet("zsync:sync:" . sys\SysRemote::VersionGet() . ":conf:entities.local.conf", '');
        admin\AdminInode::FileAction($inAction, 'conf/entities.local.conf', $cnew);
    }
    /* -------------------------------------------------------------------- */
    public static function Acronyms($inAction) {
        $cnew = sys\SysNsid::ContentsGet("zsync:sync:" . sys\SysRemote::VersionGet() . ":conf:acronyms.local.conf", '');
        admin\AdminInode::FileAction($inAction, 'conf/acronyms.local.conf', $cnew);
    }
    /* -------------------------------------------------------------------- */
    public static function Interwiki($inAction) {
        $cnew = sys\SysNsid::ContentsGet("zsync:sync:" . sys\SysRemote::VersionGet() . ":conf:interwiki.local.conf", '');
        admin\AdminInode::FileAction($inAction, 'conf/interwiki.local.conf', $cnew);
    }
    /* -------------------------------------------------------------------- */
    public static function Mime($inAction) {
        $cnew = sys\SysNsid::ContentsGet("zsync:sync:" . sys\SysRemote::VersionGet() . ":conf:mime.local.conf", '');
        admin\AdminInode::FileAction($inAction, 'conf/mime.local.conf', $cnew);
    }
    /* -------------------------------------------------------------------- */
    public static function License($inAction) {
        $ar = sys\SysNsid::IniAr("zsync:sync:" . sys\SysRemote::VersionGet() . ':conf:licenses.ini', '');
        if (!is_array($ar)) {
            echo admin\AdminXhtml::StatusGet('red', "wb.license.ini not parsable.");
            return '';
        }
        $cnew = array();
        $cnew[] = '<?php';
        foreach ($ar as $section => $ar2) {
            $cnew[] = "\$license['$section'] = array(";
            foreach ($ar2 as $id => $val) {
                $cnew[] = "   '$id' => '{$val}',";
            }
            $cnew[] = ");";
        }
        $cnew[] = '?>';
        admin\AdminInode::FileAction($inAction, 'conf/license.local.php', implode("\n", $cnew) . "\n");
    }
    /* -------------------------------------------------------------------- */
    public static function Link($inAction) {
        switch ($inAction) {
            case 'status':
                $title = 'conf/lang, conf/plugin & conf/plugin_lang';
                if (is_link('conf/lang') and is_link('conf/plugin') and is_link('conf/plugin_lang')) {
                    $status = 'green';
                } elseif (!file_exists('conf/lang') or !file_exists('conf/plugin') or !file_exists('conf/plugin_lang')) {
                    $status = 'orange';
                } else {
                    $status = 'yellow';
                }
                echo admin\AdminXhtml::StatusGet($status, $title);
                break;
            case 'relink':
                $ar = ['conf/lang', 'conf/plugin', 'conf/plugin_lang'];
                foreach ($ar as $val) {
                    if (!file_exists($val) or is_link($val)) {
                        unlink($val);
                        symlink(getcwd() . "/lib/plugins/workbook/$val", $val); // must be absolute (otherwise JS error)
                    }
                }
                break;
            case 'remove':
                $ar = ['conf/lang', 'conf/plugin', 'conf/plugin_lang'];
                foreach ($ar as $val) {
                    if (is_link($val)) {
                        unlink($val);
                    } elseif (is_dir($val)) {
                        admin\AdminInode::RmR($val . '/');
                    }
                }
                break;
            default:
                admin\AdminXhtmlMsg::Echo('Warning', __METHOD__, $inAction, 'Parameter unknown.');
                break;
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Inc($inAction) {
        admin\AdminInode::DirAction($inAction, 'inc/', 'lib/plugins/workbook/lib/' . sys\SysRemote::VersionGet() . '/inc/', '.txt');
    }
    /* -------------------------------------------------------------------- */
    public static function Lib($inAction) {
        admin\AdminInode::DirAction($inAction, 'lib/', 'lib/plugins/workbook/lib/' . sys\SysRemote::VersionGet() . '/lib/', '.txt');
    }
    /* -------------------------------------------------------------------- */
    public static function Home($inAction, $inId) {
        if (empty($inId)) return admin\AdminXhtmlMsg::Echo('Warning', __METHOD__, '', 'Id is empty.');
        $cnew = file_get_contents("lib/plugins/workbookcore/wbconf/data-pages-home/$inId");
        admin\AdminInode::FileAction($inAction, "data/pages/$inId", $cnew);
    }
    /* -------------------------------------------------------------------- */
    private static function __InisCheckGet($inAction) {
        $return = '';
        $ar = sys\SysNsid::IniAr("zsync:sync:" . sys\SysRemote::VersionGet() . ":conf:$inAction.ini", '');
        if (empty($ar)) {
            echo admin\AdminXhtml::StatusGet('red', "wb.$inAction.ini missing.");
            return '';
        }
        $installed = [];
        switch ($inAction) {
            case 'plugins':
                $installed = scandir("lib/plugins/");
                break;
            case 'tpls':
                $installed = scandir("lib/tpl/");
                break;
            case 'confs':
                $installed = array();
                break;
            default:
                admin\AdminXhtmlMsg::Echo('Warning', __METHOD__, $inAction, 'Parameter unknown.');
                break;
        }
        foreach ($installed as $id => $val) {
            if (substr($val, 0, 1) == '.') unset($installed[$id]);
            if (strpos($val, '.') !== false) unset($installed[$id]);
        }
        $plugins = [];
        if (file_exists('conf/plugins.local.php')) {
            include('conf/plugins.local.php');
        }
        if (is_array($ar['depends'])) {
            $return .= "[depends]\n";
            foreach ($ar['depends'] as $id => $val) {
                $return .= self::__CheckGet($inAction, $id, $val, '', 'Error');
                unset($installed[array_search($id, $installed)]);
            }
            $return .= "\n";
        }
        if (is_array($ar['recommends'])) {
            $return .= "[recommends]\n";
            foreach ($ar['recommends'] as $id => $val) {
                $return .= self::__CheckGet($inAction, $id, $val, '', 'Warning');
                unset($installed[array_search($id, $installed)]);
            }
            $return .= "\n";
        }
        if (is_array($ar['suggests'])) {
            $return .= "[suggests]\n";
            foreach ($ar['suggests'] as $id => $val) {
                $return .= self::__CheckGet($inAction, $id, $val, '', 'Notice');
                unset($installed[array_search($id, $installed)]);
            }
            $return .= "\n";
        }
        if (is_array($ar['conflicts'])) {
            $return .= "[conflicts]\n";
            foreach ($ar['conflicts'] as $id => $val) {
                $return .= self::__CheckGet($inAction, $id, $val, 'Error', '');
                unset($installed[array_search($id, $installed)]);
            }
            $return .= "\n";
        }
        if (is_array($ar['deprecated'])) {
            $return .= "[deprecated]\n";
            foreach ($ar['deprecated'] as $id => $val) {
                $return .= self::__CheckGet($inAction, $id, $val, 'Notice', '');
                unset($installed[array_search($id, $installed)]);
            }
            $return .= "\n";
        }
        if (is_array($ar['other'])) {
            $return .= "[other]\n";
            foreach ($ar['other'] as $id => $val) {
                switch ($inAction) {
                    case 'plugins':
                        $return .= (is_dir("lib/plugins/$id") and @$plugins[$id] !== 0) ? "Plugin $id ($val) installed\n" : "";
                        break;
                    case 'tpls':
                        $return .= (is_dir("lib/tpl/$id")) ? "Tpl $id ($val) installed\n" : "";
                        break;
                    case 'confs':
                        $return .= "Conf $id ($val)\n";
                        break;
                    default:
                        admin\AdminXhtmlMsg::Echo('Warning', __METHOD__, $inAction, 'Parameter unknown.');
                        break;
                }
                unset($installed[array_search($id, $installed)]);
            }
            $return .= "\n";
        }
        if (!empty($installed)) {
            $return .= "[unknown]\n";
            foreach ($installed as $val) {
                $return .= "[Notice] Plugin $val installed\n";
            }
            $return .= "\n";
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    private static function __CheckGet($inAction, $inId, $inVal, $inMsgTrue, $inMsgFalse) {
        $return = '';
        $msgtrue = (empty($inMsgTrue)) ? '' : "[$inMsgTrue] ";
        $msgfalse = (empty($inMsgFalse)) ? '' : "[$inMsgFalse] ";
        switch ($inAction) {
            case 'plugins':
                $plugins = [];
                if (file_exists('conf/plugins.local.php')) include('conf/plugins.local.php');
                $return .= (is_dir("lib/plugins/$inId") and @$plugins[$inId] !== 0) ? "{$msgtrue}Plugin $inId ($inVal) installed\n" : "{$msgfalse}Plugin $inId ($inVal) missing/disabled\n";
                break;
            case 'tpls':
                $return .= (is_dir("lib/tpl/$inId")) ? "{$msgtrue}Tpl $inId ($inVal) installed\n" : "{$msgfalse}Tpl $inId ($inVal) missing\n";
                break;
            case 'confs':
                $return .= (eval("global \$conf; return $inVal;")) ? "{$msgtrue}Conf $inId ($inVal) ok\n" : "{$msgfalse}Conf $inId ($inVal) failed\n";
                break;
            default:
                admin\AdminXhtmlMsg::Echo('Warning', __METHOD__, $inAction, 'Parameter unknown.');
                break;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
}