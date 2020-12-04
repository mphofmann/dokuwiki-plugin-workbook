<?php
namespace workbook\wbinc\admincore;
use workbook\wbinc\admin;
use workbookcore\wbinc\base;
use workbookcore\wbinc\env;
use workbookcore\wbinc\sys;
use workbookcore\wbinc\util;
class AdmincoreContent {
    /* -------------------------------------------------------------------- */
    public static function StartWbonlyExec($inAction = 'remove', $inNs = '') {
        $found = false;
        switch ($inAction) {
            case 'remove':
            case 'reset':
                $path = 'data/pages/' . strtr($inNs, [':' => '/']);
                if (substr($path, -1) !== '/') $path .= '/';
                foreach (util\UtilPath::Scandir($path) as $inode) {
                    if (substr($inode, -1) == '/') {
                        $ns2 = "$inNs:" . substr($inode, 0, -1);
                        $ns2 = (substr($ns2, 0, 1) == ':') ? substr($ns2, 1) : $ns2;
                        self::StartWbonlyExec($inAction, $ns2);
                    }
                    if ($inode == 'start.txt') {
                        $found = true;
                        if($inAction=='remove'){
                            if(trim(file_get_contents("{$path}{$inode}"))=='<wb/>') {
                                unlink("{$path}{$inode}");
                                base\BaseXhtmlMsg::Echo('Info', __METHOD__, $inNs, "'start' removed.");
                            }
                        }
                    }
                }
                if($inAction=='verify' and !$found) {
                    sys\SysNsid::ContentsPut("$inNs:start", 'local', util\UtilSyntax::WbTagGet());
                    base\BaseXhtmlMsg::Echo('Info', __METHOD__, $inNs, "'start' added.");
                }
                break;
            default:
                base\BaseXhtmlMsg::Add('Notice', __METHOD__, "$inAction, $inNs", "Unknown action.");
                break;
        }
    }
    /* DEPRECATED --------------------------------------------------------- */
    /* public static function NsemptyExec($inAction = 'remove', $inNs = '') {
        switch ($inAction) {
            case 'remove':
                $path = 'data/pages/' . strtr($inNs, [':' => '/']);
                if (substr($path, -1) !== '/') $path .= '/';
                foreach (util\UtilPath::Scandir($path) as $inode) {
                    if (substr($inode, -1) == '/') {
                        $ns2 = "$inNs:" . substr($inode, 0, -1);
                        $ns2 = (substr($ns2, 0, 1) == ':') ? substr($ns2, 1) : $ns2;
                        self::NsemptyExec($inAction, $ns2);
                    }
                }
                $ar = util\UtilPath::Scandir($path);
                if (count($ar) == 0) {
                    util\UtilPath::Rmdir($path);
                    base\BaseXhtmlMsg::Echo('Info', __METHOD__, $inNs, "Empty '$inNs' removed.");
                }
                break;
            default:
                base\BaseXhtmlMsg::Add('Notice', __METHOD__, "$inAction, $inNs", "Unknown action.");
                break;
        }
    } */
    /* -------------------------------------------------------------------- */
    public static function LinkExec($inAction = 'verify', $inNs = '') {
        switch ($inAction) {
            case 'verify':
                $path = 'data/pages/' . strtr($inNs, [':' => '/']);
                if (substr($path, -1) !== '/') $path .= '/';
                foreach (util\UtilPath::Scandir($path) as $inode) {
                    if (substr($inode, -1) == '/') {
                        $ns2 = "$inNs:" . substr($inode, 0, -1);
                        $ns2 = (substr($ns2, 0, 1) == ':') ? substr($ns2, 1) : $ns2;
                        self::LinkExec($inAction, $ns2);
                    } elseif (substr($inode, -4) == '.txt') {
                        $ar = util\UtilLink::MissingAr("$inNs:" . substr($inode, 0, -4));
                        if (!empty($ar)) {
                            $str = strtr($path . $inode, ['data/pages/' => '', '.txt' => '', '/' => ':']);
                            echo "<a href='?id=$str'>$str</a>: ";
                            base\BaseXhtmlMsg::Echo('Notice', __METHOD__, $inAction, "Missing: " . implode(' ', $ar));
                        }
                    }
                }
                break;
            default:
                base\BaseXhtmlMsg::Add('Notice', __METHOD__, "$inAction, $inNs", "Unknown action.");
                break;
        }
    }
    /* -------------------------------------------------------------------- */
    public static function CacheExec($inAction = 'status', $inNs = '') {
        AdmincoreOperating::WbSyncExec($inAction, $inNs);
    }
    /* -------------------------------------------------------------------- */
    public static function ZTrashExec($inAction = 'clear', $inNs = '') {
        $path = 'data/pages/' . strtr($inNs, [':' => '/']);
        if (substr($path, -1) !== '/') $path .= '/';
        $path .= 'ztrash/';
        switch ($inAction) {
            case 'clear':
                if (is_dir($path)) {
                    admin\AdminInode::Clear($path);
                }
                break;
            case 'size':
                if (!empty($path) and is_dir($path)) {
                    echo admin\AdminInode::SizeGet($path);
                }
                break;
            default:
                base\BaseXhtmlMsg::Add('Notice', __METHOD__, "$inAction, $inNs", "Unknown action.");
                break;
        }
    }
    /* -------------------------------------------------------------------- */
    public static function DownloadExec($inAction, $inNs = '') {
        switch ($inAction) {
            case 'create':
                if (empty($inNs)) {
                    $dirs = ['conf', 'data/pages/', 'data/media/', 'data/meta/'];
                } else {
                    $path = 'data/pages/' . strtr($inNs, [':' => '/']);
                    if (substr($path, -1) !== '/') $path .= '/';
                    $dirs = ["data/pages/$path", "data/media/$path"];
                }
                util\UtilPath::MkdirCheck('data/workbook/tmp/adminbackup/');
                $tardir = getcwd() . '/data/workbook/tmp/adminbackup/';
                $tarfile = date('Y-m-d-His') . '-workbook.tar';
                foreach ($dirs as $dir) {
                    $mode = (file_exists($tardir . $tarfile)) ? '--append' : '--create';
                    admin\AdminCmd::SystemEcho("tar $mode --file=$tardir$tarfile $dir");
                }
                $out = admin\AdminCmd::SystemGet("which gzip");
                if (!empty($out)) {
                    admin\AdminCmd::SystemEcho("gzip $tardir$tarfile");
                    $tarfile .= '.gz';
                }
                $user = env\EnvUserCurrent::Get();
                $linkdir = getcwd() . "/data/media/user/uprivate/{$user}_uu/";
                util\UtilPath::MkdirCheck($linkdir);
                admin\AdminCmd::SystemEcho("ln -s $tardir$tarfile $linkdir");
                base\BaseXhtmlMsg::Add('Success', '', '', "Archive create for download with a link in your media files. <a href='?do=media&ns=user:uprivate:{$user}_uu'>&raquo;&raquo;&raquo;</a>");
                break;
            case 'clear':
                admin\AdminInode::RmR('data/workbook/tmp/adminbackup/');
                $linkdir = "data/media/user/uprivate/" . env\EnvUserCurrent::Get() . "_uu/";
                admin\AdminCmd::SystemEcho("find -L $linkdir -type l -delete");
                break;
            default:
                base\BaseXhtmlMsg::Echo('Warning', __METHOD__, $inAction, 'Parameter unknown.');
                break;
        }
    }    /* -------------------------------------------------------------------- */
}