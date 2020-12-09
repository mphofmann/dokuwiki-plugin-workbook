<?php
namespace workbook\wbinc\admincore;
use workbook\wbinc\admin;
use workbookcore\wbinc\base;
use workbookcore\wbinc\util;
use workbookcore\wbinc\xhtml;
class AdmincoreOperating {
    /* -------------------------------------------------------------------- */
    public static function DokuCacheExec($inAction = 'status') {
        switch ($inAction) {
            case 'status':
                echo date('Y-m-d His', filemtime('data/cache'));
                break;
            case 'purge':
                admin\AdminCmd::SystemEcho('touch conf/local.php');
                break;
            case 'clear':
                admin\AdminInode::Clear('data/cache/');
                admin\AdminCmd::SystemEcho('touch conf/local.php');
                break;
            default:
                base\BaseXhtmlMsg::Add('Notice', __METHOD__, $inAction, "Unknown action.");
                break;
        }
    }
    /* -------------------------------------------------------------------- */
    public static function WbSyncExec($inAction = 'status', $inNs = '') {
        $pathsyncsource = util\UtilPath::Get('syncsource');
        $pathsyncxhtml = util\UtilPath::Get('syncxhtml');
        $p = strtr($inNs, [':' => '/']);
        switch ($inAction) {
            case 'size':
                if (is_dir($pathsyncsource . 'pages/' . $p)) {
                    echo admin\AdminInode::SizeGet($pathsyncsource . 'pages/' . $p);
                } elseif (is_dir($pathsyncxhtml . 'pages/' . $p)) {
                    echo admin\AdminInode::SizeGet($pathsyncxhtml . 'pages/' . $p);
                }
                break;
            case 'clear':
                admin\AdminInode::RmR($pathsyncsource . 'pages/' . $p);
                admin\AdminInode::RmR($pathsyncsource . 'media/' . $p);
                admin\AdminInode::RmR($pathsyncxhtml . 'pages/' . $p);
                admin\AdminInode::RmR($pathsyncxhtml . 'media/' . $p);
                break;
            case 'status':
                if (is_dir($pathsyncsource . 'pages/' . $p)) {
                    echo date('Y-m-d His', filemtime($pathsyncsource . 'pages/' . $p));
                } elseif (is_dir($pathsyncxhtml . 'pages/' . $p)) {
                    echo date('Y-m-d His', filemtime($pathsyncxhtml . 'pages/' . $p));
                } else {
                    echo xhtml\XhtmlUnicode::StatusGet('red');
                }
                break;
            default:
                base\BaseXhtmlMsg::Add('Notice', __METHOD__, "$inAction, $inNs", "Unknown action.");
                break;
        }
    }
    /* -------------------------------------------------------------------- */
    public static function WbConfExec($inAction = 'status', $inScope = '') {
        switch ($inAction) {
            case 'status':
                switch ($inScope) {
                    case 'basic':
                        AdmincoreConf::Plugins('status');
                        AdmincoreConf::Tpls('status');
                        AdmincoreConf::Confs('status');
                        break;
                    case 'conf':
                        $methods = ['LocalProtected', 'Acl', 'Entities', 'Acronyms', 'Interwiki', 'Mime', 'License', 'Lang'];
                        foreach ($methods as $method) {
                            if (base\Base::MethodExists('admincore\AdmincoreConf', $method)) AdmincoreConf::$method('status');
                        }
                        break;
                    case 'home-files':
                        $ar = util\UtilPath::Scandir('lib/plugins/workbookcore/wbconf/data-pages-home/');
                        if (empty($ar)) {
                            echo xhtml\XhtmlUnicode::StatusGet('red', 'Files are missing.');
                            return;
                        }
                        foreach ($ar as $val) {
                            AdmincoreConf::Home('status', $val);
                        }
                        break;
                    default:
                        base\BaseXhtmlMsg::Echo('Warning', __METHOD__, $inAction, 'Parameter unknown.');
                        break;
                }
                break;
            default:
                base\BaseXhtmlMsg::Add('Notice', __METHOD__, "$inAction, $inScope", "Unknown action.");
                break;
        }
    }
    // DEPRECATED --------------------------------------------------------
    public static function DokuCleanupExec($inAction = '') {
        switch ($inAction) {
            case 'cleanup':
                $datapath = './data';
                $days = 365;
                admin\AdminCmd::SystemEcho("find '{$datapath}'/{media_,}attic/ -type f - mtime +$days -delete 2>&1");  // purge files older than $inDays days from attic and media_attic (old revisions)
                admin\AdminCmd::SystemEcho("find '{$datapath}'/locks/ -name '*.lock' -type f - mtime +1 -delete 2>&1");  // remove stale lock files (files which are 1-2 days old)
                admin\AdminCmd::SystemEcho("find '{$datapath}'/{attic,cache,index,locks,media,media_attic,media_meta,meta,pages,tmp}/ -mindepth 1 -type d -empty -delete 2>&1"); // remove empty directories
                admin\AdminCmd::SystemEcho("find '{$datapath}'/cache/?/ -type f -mtime +$days -delete 2>&1"); // remove files older than $inDays days from the cache
                break;
            default:
                base\BaseXhtmlMsg::Add('Warning', __METHOD__, $inAction, 'Parameter unknown.');
                break;
        }
    }
    /* -------------------------------------------------------------------- */
}