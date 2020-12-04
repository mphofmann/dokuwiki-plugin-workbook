<?php
namespace workbook\wbinc\dokuadmin;
use workbook\wbinc\admin;
use workbook\wbinc\doku;
class DokuadminInstall {
    /* -------------------------------------------------------------------- */
    public static $Infras = [ //
        'PHP' => '', //
        'PHP-Ioncube' => '', //
        'Webroot' => '', //
        'Dokuwiki' => '', //
    ];
    public static $Extensions = [ //
        'plugins' => [ //
            // MPH
            'workbook' => 'deb:', // 'https://github.com/mphofmann/dokuwiki-plugin-workbook/archive/main.zip', //
            'workbookcore' => 'deb:', //
            'workbookuseracl' => 'deb:', //
            // CosmoCode
            'cleanup' => 'https://github.com/cosmocode/dokuwiki-plugin-cleanup/archive/master.zip', //
            'confmanager' => 'https://github.com/cosmocode/confmanager/archive/master.zip', //
            'edittable' => 'https://github.com/cosmocode/edittable/archive/master.zip', //
            'sqlite' => 'https://github.com/cosmocode/sqlite/archive/master.zip', //
            'struct' => 'https://github.com/cosmocode/dokuwiki-plugin-struct/archive/master.zip', //
            // Splitbrain
            'captcha' => 'https://github.com/splitbrain/dokuwiki-plugin-captcha/archive/master.zip', //
            'passpolicy' => 'https://github.com/splitbrain/dokuwiki-plugin-passpolicy/archive/master.zip', //
            'searchindex' => 'https://github.com/splitbrain/dokuwiki-plugin-searchindex/archive/master.zip', //
            'upgrade' => 'https://github.com/splitbrain/dokuwiki-plugin-upgrade/archive/master.zip', //
            // Michitux
            'move' => 'https://github.com/michitux/dokuwiki-plugin-move/archive/master.zip', //
            // Optional
            // 'dropfiles' => 'https://github.com/cosmocode/dokuwiki-plugin-dropfiles/archive/master.zip', //
            'feedback' => 'https://github.com/cosmocode/dokuwiki-plugin-feedback/archive/master.zip', //
            'prosemirror' => 'manual:,note:cosmocode', //
            // 'structgantt' => 'https://github.com/cosmocode/dokuwiki-plugin-structgantt/archive/master.zip', //
            'tablelayout' => 'https://github.com/cosmocode/dokuwiki-plugin-tablelayout/archive/master.zip', //
            'toolbox' => 'https://github.com/splitbrain/dokuwiki-plugin-toolbox/archive/master.zip', //
        ], //
        'templates' => [ //
            // MPH
            'workbook' => 'deb:', //
        ], //
    ];
    public static $Webroots = [ //
        'index.php' => 'Controller switcher', //
        // 'doku.php' => 'Dokuwiki controller', //
        'wb.php' => 'Workbook controller', //
    ];
    public static $Confs = [ //
        'local.protected.php' => 'Confs protected', //
        'acl.auth.php' => 'ACL rules', //
    ];
    /* -------------------------------------------------------------------- */
    public static function InfraAction($inAction, $inId) {
        $return = '';
        switch ($inAction) {
            case 'note':
                $return .= self::$Infras[$inId];
                break;
            case 'status':
                $ar = ['PHP' => 'php-ok', 'PHP-Ioncube' => 'ioncube-ok', 'Dokuwiki' => 'dokuwiki-ok', 'Webroot' => 'webroot-ok'];
                $color = strpos(WB_RUNMODE, $ar[$inId]) === false ? 'red' : 'green';
                $return .= admin\AdminXhtml::StatusGet($color);
                break;
            case 'info':
                switch ($inId) {
                    case 'PHP':
                        phpinfo();
                        exit;
                        break;
                    case 'PHP-Ioncube':
                        $return .= extension_loaded('ioncube Loader') ? 'PHP-Ioncube extension loaded' : 'PHP-Ioncube extension missing';
                        break;
                    case 'Webroot':
                        $return .= strpos(WB_RUNMODE, 'webroot-ok') === false ? 'Webroot (' . getcwd() . ') is not writeable.' : 'Webroot (' . getcwd() . ') is writeable.';
                        break;
                    case 'Dokuwiki':
                        $return .= "See <a href='http://www.dokuwiki.org' target='_blank'>Dokuwiki</a>";
                        break;
                }
                break;
            case 'version':
                switch ($inId) {
                    case 'PHP':
                        $return .= PHP_VERSION;
                        break;
                    case 'Dokuwiki':
                        $return .= doku\DokuGlobal::VersionGet();
                        break;
                }
                break;
            default:
                doku\DokuXhtmlMsg::Echo('Warning', '', '', "Infra action unknown: $inAction $inId");
                break;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function WebrootAction($inAction, $inFilepath) {
        $return = '';
        switch ($inAction) {
            case 'install':
                $filepath = "lib/plugins/workbookcore/wbconf/$inFilepath";
                if (file_exists($filepath)) {
                    if (file_exists($inFilepath) and !file_exists("$inFilepath.orig")) {
                        copy($inFilepath, "$inFilepath.orig");
                    }
                    copy($filepath, $inFilepath);
                } else {
                    admin\AdminXhtmlMsg::Echo('Warning', '', '', "File '$filepath' is missing.");
                }
                break;
            case 'link':
                $filepath = "lib/plugins/workbookcore/wbconf/$inFilepath";
                if (file_exists($filepath)) {
                    if (is_file($inFilepath) and !file_exists("$inFilepath.orig")) {
                        copy($inFilepath, "$inFilepath.orig");
                    }
                    if (file_exists($inFilepath)) unlink($inFilepath);
                    symlink($filepath, $inFilepath);
                } else {
                    admin\AdminXhtmlMsg::Echo('Warning', '', '', "File '$filepath' is missing.");
                }
                break;
            case 'remove':
                $return .= unlink($inFilepath);
                break;
            case 'restore':
                if (file_exists("$inFilepath.orig")) {
                    unlink($inFilepath);
                    copy("$inFilepath.orig", $inFilepath);
                    unlink("$inFilepath.orig");
                } else {
                    admin\AdminXhtmlMsg::Echo('Warning', '', '', "File '$inFilepath.orig' is missing.");
                }
                break;
            case 'status':
                $rc = (strpos("index.php wb.php", $inFilepath) === false) ? file_exists($inFilepath) : is_link($inFilepath);
                $color = $rc ? 'green' : 'red';
                $return .= admin\AdminXhtml::StatusGet($color);
                break;
            default:
                admin\AdminXhtmlMsg::Echo('Warning', '', '', "Action unknown: $inAction $inFilepath");
                break;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function ExtensionAction($inAction, $inType, $inId, $inUrl = '', $inTarpath = '') {
        $return = '';
        switch ($inAction) {
            case 'note':
                $return .= self::ExtensionNoteGet($inType, $inId, $inUrl);
                break;
            case 'install':
                $return .= self::ExtensionInstall($inType, $inId, $inUrl, $inTarpath);
                break;
            case 'remove':
                break;
            case 'status':
                $extpath = self::__ExtensionPathGet($inType, $inId);
                $color = is_dir($extpath) ? 'green' : 'red';
                $return .= admin\AdminXhtml::StatusGet($color);
                break;
            default:
                doku\DokuXhtmlMsg::Echo('Warning', '', '', "Extension action unknown: $inAction $inType $inId");
                break;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function ExtensionNoteGet($inType, $inId, $inUrl = '') {
        $return = '';
        $url = self::__ExtensionUrlGet($inType, $inId, $inUrl);
        $ar = [ //
            'workbook' => '<span title="Michael P. Hofmann AG, Rapperswil, Switzerland">by MPH</span>', '.deb' => '<span title="Michael P. Hofmann AG, Rapperswil, Switzerland">by MPH</span>', 'deb:' => '<span title="Michael P. Hofmann AG, Rapperswil, Switzerland">by MPH</span>', //
            'splitbrain' => '<span title="Andrea Gohr, Berlin, Germany">by Splitbrain</span>', //
            'cosmocode' => '<span title="CosmoCode GmbH, Berlin, Germany">by CosmoCode</span>', //
            'michitux' => '<span title="Michael Hamann, Karlsruhe, Germany">by Hamann</span>', //
        ];
        foreach ($ar as $id => $val) {
            if (strpos($url, $id) !== false) {
                $return .= $val;
                break;
            }
        }
        if (empty($return)) $return .= $url;
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function ExtensionInstall($inType, $inId, $inUrl = '', $inTarpath = '') {
        $url = self::__ExtensionUrlGet($inType, $inId, $inUrl);
        if (empty($url)) return doku\DokuXhtmlMsg::Echo('Warning', '', '', "Url missing: $inType-$inId");
        if (substr($url, -4) == '.deb' and empty($inTarpath)) return doku\DokuXhtmlMsg::Echo('Warning', '', '', "Tarpath is empty: $inType-$inId");
        // download
        $dirpath = "data/tmp/dir" . uniqid() . "/";
        admin\AdminInode::MkdirCheck($dirpath);
        $rc = file_put_contents("{$dirpath}download", file_get_contents($url));
        if ($rc === false) return doku\DokuXhtmlMsg::Echo('Warning', '', '', "Download failed: $inType-$inId");
        // extract
        switch (substr($url, -4)) {
            case '.zip':
                $zip = new \ZipArchive;
                $res = $zip->open("{$dirpath}download");
                if ($res === false) return doku\DokuXhtmlMsg::Echo('Warning', '', '', "Extracting failed: $inType-$inId");
                $zip->extractTo($dirpath);
                $zip->close();
                unlink("{$dirpath}download");
                doku\DokuXhtmlMsg::Echo('Info', '', '', "Extension downloaded: $inType-$inId");
                break;
            case '.deb':
                system("cd $dirpath; ar -x download data.tar.xz 2>&1");
                system("cd $dirpath; tar -xf data.tar.xz .{$inTarpath} 2>&1");
                unlink("{$dirpath}download");
                unlink("{$dirpath}data.tar.xz");
                doku\DokuXhtmlMsg::Echo('Info', '', '', "Extension downloaded: $inType-$inId");
                break;
        }
        // move
        $extpath = self::__ExtensionPathGet($inType, $inId);
        switch (substr($url, -4)) {
            case '.zip':
                foreach (scandir($dirpath) as $inode) {
                    if (substr($inode, 0, 1) == '.') continue;
                    self::__ExtensionRemove($inType, $inId);
                    system("mv {$dirpath}{$inode} {$extpath} 2>&1");
                    doku\DokuXhtmlMsg::Echo('Info', '', '', "Extension copied: $inType-$inId");
                    break;
                }
                break;
            case '.deb':
                system("mv {$dirpath}{$inTarpath} {$extpath} 2>&1");
                doku\DokuXhtmlMsg::Echo('Info', '', '', "Extension copied: $inType-$inId");
                break;
        }
        // cleanup
        system("rm -R $dirpath 2>&1");
        admin\AdminCmd::SystemEcho('touch conf/local.php');
        doku\DokuXhtmlMsg::Echo('Success', '', '', "Extension installed: $inType-$inId");
    }
    /* -------------------------------------------------------------------- */
    private static function __ExtensionRemove($inType, $inId) {
        $extpath = self::__ExtensionPathGet($inType, $inId);
        if (is_dir($extpath)) {
            admin\AdminInode::RmR($extpath);
            doku\DokuXhtmlMsg::Echo('Info', '', '', "Extension removed: $inType-$inId");
        }
    }
    /* -------------------------------------------------------------------- */
    private static function __ExtensionUrlGet($inType, $inId, $inUrl) {
        $return = $inUrl;
        if (empty($return)) $return = @self::$Extensions[$inType][$inId];
        $return = strtr($return, ['$ID$' => $inId]);
        return $return;
    }
    /* -------------------------------------------------------------------- */
    private static function __ExtensionPathGet($inType, $inId) {
        return $inType == 'templates' ? "lib/tpl/$inId" : "lib/plugins/$inId";
    }
    /* -------------------------------------------------------------------- */
}