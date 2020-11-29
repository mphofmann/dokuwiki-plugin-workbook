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
            'workbook' => 'deb:sappinternal-appsource-dokuwiki:/opt/sapp/sapp-app/dokuwiki/source/addon-plugins-71wb-public/$ID$/all/lib/plugins/$ID$/', // 'https://github.com/mphofmann/dokuwiki-plugin-workbook/archive/main.zip', //
            'workbookcore' => 'deb:sappinternal-appsource-dokuwiki:/opt/sapp/sapp-app/dokuwiki/source/addon-plugins-71wb-public/$ID$/all/lib/plugins/$ID$/', //
            'workbookuser' => 'deb:sappinternal-appsource-dokuwiki:/opt/sapp/sapp-app/dokuwiki/source/addon-plugins-71wb-public/$ID$/all/lib/plugins/$ID$/', //
            // CosmoCode
            'cleanup' => 'https://github.com/cosmocode/dokuwiki-plugin-cleanup/archive/master.zip', //
            'confmanager' => 'https://github.com/cosmocode/confmanager/archive/master.zip', //
            'dropfiles' => 'https://github.com/cosmocode/dokuwiki-plugin-dropfiles/archive/master.zip', //
            'edittable' => 'https://github.com/cosmocode/edittable/archive/master.zip', //
            'feedback' => 'https://github.com/cosmocode/dokuwiki-plugin-feedback/archive/master.zip', //
            'prosemirror' => 'https://github.com/cosmocode/dokuwiki-plugin-prosemirror/archive/master.zip', //
            'sqlite' => 'https://github.com/cosmocode/sqlite/archive/master.zip', //
            'struct' => 'https://github.com/cosmocode/dokuwiki-plugin-struct/archive/master.zip', //
            'structgantt' => 'https://github.com/cosmocode/dokuwiki-plugin-structgantt/archive/master.zip', //
            'tablelayout' => 'https://github.com/cosmocode/dokuwiki-plugin-tablelayout/archive/master.zip', //
            // splitbrain
            'captcha' => 'https://github.com/splitbrain/dokuwiki-plugin-captcha/archive/master.zip', //
            'passpolicy' => 'https://github.com/splitbrain/dokuwiki-plugin-passpolicy/archive/master.zip', //
            'searchindex' => 'https://github.com/splitbrain/dokuwiki-plugin-searchindex/archive/master.zip', //
            'toolbox' => 'https://github.com/splitbrain/dokuwiki-plugin-toolbox/archive/master.zip', //
            'upgrade' => 'https://github.com/splitbrain/dokuwiki-plugin-upgrade/archive/master.zip', //
            // michitux
            'move' => 'https://github.com/michitux/dokuwiki-plugin-move/archive/master.zip', //
        ], //
        'templates' => [ //
            // MPH
            'workbook' => 'deb:sappinternal-appsource-dokuwiki:/opt/sapp/sapp-app/dokuwiki/source/addon-tpl-71wb-public/$ID$/all/lib/tpl/$ID$/', //
        ], //
    ];
    public static $WbExtensions = [ //
        'plugins' => [ //
            // 73wb-inc
            'workbookinc' => 'deb:sappinternal-appsource-dokuwikiworkbook:/opt/sapp/sapp-app/dokuwiki/source/addon-plugins-73wb-inc/$ID$/all/lib/plugins/$ID$/', //
            // 73wb-tag
            'workbookconference' => 'deb:sappinternal-appsource-dokuwikiworkbook:/opt/sapp/sapp-app/dokuwiki/source/addon-plugins-73wb-tag/$ID$/all/lib/plugins/$ID$/', //
            'workbookdo' => 'deb:sappinternal-appsource-dokuwikiworkbook:/opt/sapp/sapp-app/dokuwiki/source/addon-plugins-73wb-tag/$ID$/all/lib/plugins/$ID$/', //
            'workbookmedia' => 'deb:sappinternal-appsource-dokuwikiworkbook:/opt/sapp/sapp-app/dokuwiki/source/addon-plugins-73wb-tag/$ID$/all/lib/plugins/$ID$/', //
            'workbookslide' => 'deb:sappinternal-appsource-dokuwikiworkbook:/opt/sapp/sapp-app/dokuwiki/source/addon-plugins-73wb-tag/$ID$/all/lib/plugins/$ID$/', //
            'workbooktag' => 'deb:sappinternal-appsource-dokuwikiworkbook:/opt/sapp/sapp-app/dokuwiki/source/addon-plugins-73wb-tag/$ID$/all/lib/plugins/$ID$/', //
            'workbookwbtool' => 'deb:sappinternal-appsource-dokuwikiworkbook:/opt/sapp/sapp-app/dokuwiki/source/addon-plugins-73wb-tag/$ID$/all/lib/plugins/$ID$/', //
            // 73wb-tag-external
            'workbookdisqus' => 'deb:sappinternal-appsource-dokuwikiworkbook:/opt/sapp/sapp-app/dokuwiki/source/addon-plugins-73wb-tag-external/$ID$/all/lib/plugins/$ID$/', //
            'workbookgoogle' => 'deb:sappinternal-appsource-dokuwikiworkbook:/opt/sapp/sapp-app/dokuwiki/source/addon-plugins-73wb-tag-external/$ID$/all/lib/plugins/$ID$/', //
            'workbooksapp' => 'deb:sappinternal-appsource-dokuwikiworkbook:/opt/sapp/sapp-app/dokuwiki/source/addon-plugins-73wb-tag-external/$ID$/all/lib/plugins/$ID$/', //
            'workbookzapier' => 'deb:sappinternal-appsource-dokuwikiworkbook:/opt/sapp/sapp-app/dokuwiki/source/addon-plugins-73wb-tag-external/$ID$/all/lib/plugins/$ID$/', //
        ], //
    ];
    public static $Webroots = [ //
        'index.php' => 'Controller switcher', //
        'doku.php' => 'Dokuwiki controller', //
        'wb.php' => 'Workbook controller', //
    ];
    public static $Confs = [ //
        'local.protected.php' => 'Confs protected', //
        'acl.auth.php' => 'ACL rules', //
    ];
    private static $__DebUrl = ''; //
    /* -------------------------------------------------------------------- */
    public static function __constructStatic() {
        $url = doku\DokuGlobal::ConfGet('plugin', 'workbook', 'remote_install_url');
        if (empty($url) or $url == '!!not set!!') {
            doku\DokuXhtmlMsg::Add('Notice', '', '', "Remote-Install-Url not set.");
        } else {
            self::$__DebUrl = $url;
        }
    }
    /* -------------------------------------------------------------------- */
    public static function All($inAction) {
        $return = '';
        switch ($inAction) {
            case 'install':
                $return .= "TODO $inAction - not implemented yet.";
                break;
            default:
                doku\DokuXhtmlMsg::Echo('Warning', __METHOD__, '', "All action unknown: $inAction");
                break;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function Infra($inAction, $inId) {
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
                        $return .= file_get_contents('VERSION');
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
    public static function Extension($inAction, $inType, $inId) {
        $return = '';
        switch ($inAction) {
            case 'note':
                $return .= self::__ExtensionNoteGet($inType, $inId);
                break;
            case 'install':
                $return .= self::__ExtensionInstall($inType, $inId);
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
    public static function Webroot($inAction, $inId) {
        return self::__WbConfAction($inAction, $inId, self::$Webroots[$inId]);
    }
    /* -------------------------------------------------------------------- */
    public static function Conf($inAction, $inId) {
        return self::__WbConfAction($inAction, "conf/$inId", self::$Confs[$inId]);
    }
    /* -------------------------------------------------------------------- */
    private static function __WbconfAction($inAction, $inFilepath, $inNote) {
        $return = '';
        switch ($inAction) {
            case 'note':
                $return .= $inNote;
                break;
            case 'install':
                $filepath = "lib/plugins/workbookcore/wbconf/$inFilepath";
                if (file_exists($filepath)) {
                    if (file_exists($inFilepath) and !file_exists("$inFilepath.orig")) {
                        copy($inFilepath, "$inFilepath.orig");
                    }
                    copy($filepath, $inFilepath);
                } else {
                    doku\DokuXhtmlMsg::Echo('Warning', '', '', "File '$filepath' is missing.");
                }
                break;
            case 'remove':
                $return .= unlink($inFilepath);
                break;
            case 'restore':
                if (file_exists("$inFilepath.orig")) {
                    copy("$inFilepath.orig", $inFilepath);
                    unlink("$inFilepath.orig");
                } else {
                    doku\DokuXhtmlMsg::Echo('Warning', '', '', "File '$inFilepath.orig' is missing.");
                }
                break;
            case 'status':
                $color = file_exists($inFilepath) ? 'green' : 'red';
                $return .= admin\AdminXhtml::StatusGet($color);
                break;
            default:
                doku\DokuXhtmlMsg::Echo('Warning', '', '', "Action unknown: $inAction $inFilepath");
                break;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    private static function __ExtensionNoteGet($inType, $inId) {
        $return = '';
        $string = isset(self::$Extensions[$inType][$inId]) ? self::$Extensions[$inType][$inId] : self::$WbExtensions[$inType][$inId];
        $ar = [ //
            'workbook' => '<span title="Michael P. Hofmann AG, Rapperswil, Switzerland">by MPH</span>', 'deb:' => '<span title="Michael P. Hofmann AG, Rapperswil, Switzerland">by MPH</span>', //
            'splitbrain' => '<span title="Andrea Gohr, Berlin, Germany">by Splitbrain</span>', //
            'cosmocode' => '<span title="CosmoCode GmbH, Berlin, Germany">by CosmoCode</span>', //
            'michitux' => '<span title="Michael Hamann, Karlsruhe, Germany">by Hamann</span>', //
        ];
        foreach ($ar as $id => $val) {
            if (strpos($string, $id) !== false) {
                $return .= $val;
                break;
            }
        }
        if (empty($return)) $return .= $string;
        return $return;
    }
    /* -------------------------------------------------------------------- */
    private static function __ExtensionInstall($inType, $inId) {
        if (empty(self::$__DebUrl)) return doku\DokuXhtmlMsg::Echo('Warning', '', '', "Remote-Install-Url is empty.");
        $urlcode = isset(self::$Extensions[$inType][$inId]) ? self::$Extensions[$inType][$inId] : self::$WbExtensions[$inType][$inId];
        $urlcode = strtr($urlcode, ['$ID$' => $inId]);
        if (substr($urlcode, -4) == '.zip') {
            $ext = 'zip';
            $url = $urlcode;
        } elseif (substr($urlcode, 0, 4) == 'deb:') {
            list($ext, $repo, $tarpath) = explode(':', $urlcode);
            $url = self::$__DebUrl . "{$repo}.{$ext}";
        }
        if (!empty($url)) {
            // download
            $dirpath = "data/tmp/dir" . uniqid() . "/";
            admin\AdminInode::MkdirCheck($dirpath);
            $rc = file_put_contents("{$dirpath}download", file_get_contents($url));
            if ($rc === false) return doku\DokuXhtmlMsg::Echo('Warning', '', '', "Download failed: $inType-$inId");
            // extract
            switch ($ext) {
                case 'zip':
                    $zip = new \ZipArchive;
                    $res = $zip->open("{$dirpath}download");
                    if ($res === false) return doku\DokuXhtmlMsg::Echo('Warning', '', '', "Extracting failed: $inType-$inId");
                    $zip->extractTo($dirpath);
                    $zip->close();
                    unlink("{$dirpath}download");
                    doku\DokuXhtmlMsg::Echo('Info', '', '', "Extension downloaded: $inType-$inId");
                    break;
                case 'deb':
                    system("cd $dirpath; ar -x download data.tar.xz 2>&1");
                    system("cd $dirpath; tar -xf data.tar.xz .{$tarpath} 2>&1");
                    unlink("{$dirpath}download");
                    unlink("{$dirpath}data.tar.xz");
                    doku\DokuXhtmlMsg::Echo('Info', '', '', "Extension downloaded: $inType-$inId");
                    break;
            }
            // move
            $extpath = self::__ExtensionPathGet($inType, $inId);
            switch ($ext) {
                case 'zip':
                    foreach (scandir($dirpath) as $inode) {
                        if (substr($inode, 0, 1) == '.') continue;
                        self::__ExtensionRemove($inType, $inId);
                        system("mv {$dirpath}{$inode} {$extpath} 2>&1");
                        doku\DokuXhtmlMsg::Echo('Info', '', '', "Extension copied: $inType-$inId");
                        break;
                    }
                    break;
                case 'deb':
                    system("mv {$dirpath}{$tarpath} {$extpath} 2>&1");
                    doku\DokuXhtmlMsg::Echo('Info', '', '', "Extension copied: $inType-$inId");
                    break;
            }
            // cleanup
            system("rm -R $dirpath 2>&1");
            admin\AdminCmd::SystemEcho('touch conf/local.php');
            doku\DokuXhtmlMsg::Echo('Success', '', '', "Extension installed: $inType-$inId");
        }
    }
    /* -------------------------------------------------------------------- */
    private static function __ExtensionRemove($inType, $inId) {
        $extpath = self::__ExtensionPathGet($inType, $inId);
        if (is_dir($extpath)) {
            admin\AdminInode::RmR($extpath);
            doku\DokuXhtmlMsg::Echo('Info', '', '', "Extension removed: $inType $inId");
        }
    }
    /* -------------------------------------------------------------------- */
    private static function __ExtensionPathGet($inType, $inId) {
        return $inType == 'templates' ? "lib/tpl/$inId" : "lib/plugins/$inId";
    }
    /* -------------------------------------------------------------------- */
}