<?php
namespace workbook\wbinc\dokuadmin;
use workbook\wbinc\admin;
use workbook\wbinc\doku;
class DokuadminExtension {
    /* -------------------------------------------------------------------- */
    public static $Extensions = [ //
        'depends' => [ //
            'plugins' => [ //
                // MPH
                'workbook' => 'https://github.com/mphofmann/dokuwiki-plugin-workbook/archive/main.zip', // plus deb:
                'workbookcore' => 'deb:', //
                'workbookuseracl' => 'deb:', //
                // CosmoCode
                'edittable' => 'https://github.com/cosmocode/edittable/archive/master.zip', //
                'sqlite' => 'https://github.com/cosmocode/sqlite/archive/master.zip', //
                'struct' => 'https://github.com/cosmocode/dokuwiki-plugin-struct/archive/master.zip', //
                // Michitux
                'move' => 'https://github.com/michitux/dokuwiki-plugin-move/archive/master.zip', //
            ], //
            'templates' => [ //
                // MPH
                'workbook' => 'deb:', //
            ], //
        ], //
        'recommends' => [ //
            'plugins' => [ //
                'captcha' => 'https://github.com/splitbrain/dokuwiki-plugin-captcha/archive/master.zip', //
                'cleanup' => 'https://github.com/cosmocode/dokuwiki-plugin-cleanup/archive/master.zip', //
                'confmanager' => 'https://github.com/cosmocode/confmanager/archive/master.zip', //
                'passpolicy' => 'https://github.com/splitbrain/dokuwiki-plugin-passpolicy/archive/master.zip', //
                'searchindex' => 'https://github.com/splitbrain/dokuwiki-plugin-searchindex/archive/master.zip', //
                'upgrade' => 'https://github.com/splitbrain/dokuwiki-plugin-upgrade/archive/master.zip', //
            ], //
        ], //
        'suggests' => [ //
            'plugins' => [ //
                // 'dropfiles' => 'https://github.com/cosmocode/dokuwiki-plugin-dropfiles/archive/master.zip', //
                'feedback' => 'https://github.com/cosmocode/dokuwiki-plugin-feedback/archive/master.zip', //
                'prosemirror' => 'manual:,note:cosmocode', //
                // 'structgantt' => 'https://github.com/cosmocode/dokuwiki-plugin-structgantt/archive/master.zip', //
                // 'tablelayout' => 'https://github.com/cosmocode/dokuwiki-plugin-tablelayout/archive/master.zip', //
                'toolbox' => 'https://github.com/splitbrain/dokuwiki-plugin-toolbox/archive/master.zip', //
            ], //
        ], //
    ];
    /* -------------------------------------------------------------------- */
    public static function Action($inAction, $inType, $inId, $inUrl = '', $inTarpath = '') {
        $return = '';
        switch ($inAction) {
            case 'note':
                $return .= self::NoteGet($inType, $inId, $inUrl);
                break;
            case 'install':
                if (is_dir("lib/plugins/$inId")) {
                    doku\DokuXhtmlMsg::Echo('Warning', '', '', "Extension already installed: $inAction $inType $inId");
                } else {
                    $return .= self::Replace($inType, $inId, $inUrl, $inTarpath);
                }
                break;
            case 'replace':
                $return .= self::Replace($inType, $inId, $inUrl, $inTarpath);
                break;
            case 'remove':
                break;
            case 'status':
                $color = 'red';
                $title = 'Not installed';
                $extpath = self::__PathGet($inType, $inId);
                if (is_dir($extpath)) {
                    $extmtime = filemtime($extpath);
                    $color = 'green';
                    $title = "Installed: " . date('Y-m-d H:i:s', $extmtime);
                    if (!empty($inUrl)) {
                        $url = self::__UrlGet($inType, $inId, $inUrl);
                        $urlmtime = admin\AdminUtil::UrlMtime($url);
                        if ($extmtime < $urlmtime) {
                            $color = 'yellow';
                            $title .= " / Updateable: " . date('Y-m-d H:i:s', $urlmtime);
                        }
                    }
                }
                $return .= admin\AdminXhtml::StatusGet($color, $title);
                break;
            default:
                doku\DokuXhtmlMsg::Echo('Warning', '', '', "Extension action unknown: $inAction $inType $inId");
                break;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function NoteGet($inType, $inId, $inUrl = '') {
        $return = '';
        $ar = [ //
            'workbook' => '<span title="Michael P. Hofmann AG, Rapperswil, Switzerland">by Manageopedia</span>', '.deb' => '<span title="Michael P. Hofmann AG, Rapperswil, Switzerland">by Manageopedia</span>', 'deb:' => '<span title="Michael P. Hofmann AG, Rapperswil, Switzerland">by Manageopedia</span>', //
            'splitbrain' => '<span title="Andrea Gohr, Berlin, Germany">by Splitbrain</span>', //
            'cosmocode' => '<span title="CosmoCode GmbH, Berlin, Germany">by CosmoCode</span>', //
            'michitux' => '<span title="Michael Hamann, Karlsruhe, Germany">by Hamann</span>', //
        ];
        $url = self::__UrlGet($inType, $inId, $inUrl);
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
    public static function Replace($inType, $inId, $inUrl = '', $inTarpath = '') {
        $url = self::__UrlGet($inType, $inId, $inUrl);
        if (empty($url)) return doku\DokuXhtmlMsg::Echo('Warning', '', '', "Url missing: $inType-$inId");
        if (substr($url, -4) == '.deb' and empty($inTarpath)) return doku\DokuXhtmlMsg::Echo('Warning', '', '', "Tarpath is empty: $inType-$inId");
        // download
        $dirpath = "data/tmp/dir" . uniqid() . "/";
        admin\AdminInode::MkdirCheck($dirpath);
        $rc = @file_put_contents("{$dirpath}download", file_get_contents($url));
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
        $extpath = self::__PathGet($inType, $inId);
        switch (substr($url, -4)) {
            case '.zip':
                foreach (scandir($dirpath) as $inode) {
                    if (substr($inode, 0, 1) == '.') continue;
                    self::__Remove($inType, $inId);
                    system("mv {$dirpath}{$inode} {$extpath} 2>&1");
                    touch($extpath);
                    doku\DokuXhtmlMsg::Echo('Info', '', '', "Extension copied: $inType-$inId");
                    break;
                }
                break;
            case '.deb':
                self::__Remove($inType, $inId);
                system("mv {$dirpath}{$inTarpath} {$extpath} 2>&1");
                touch($extpath);
                doku\DokuXhtmlMsg::Echo('Info', '', '', "Extension copied: $inType-$inId");
                break;
        }
        // cleanup
        system("rm -R $dirpath 2>&1");
        admin\AdminCmd::SystemEcho('touch conf/local.php');
        doku\DokuXhtmlMsg::Echo('Success', '', '', "Extension installed: $inType-$inId");
    }
    /* -------------------------------------------------------------------- */
    private static function __Remove($inType, $inId) {
        $extpath = self::__PathGet($inType, $inId);
        if (is_dir($extpath)) {
            admin\AdminInode::RmR($extpath);
            doku\DokuXhtmlMsg::Echo('Info', '', '', "Extension removed: $inType-$inId");
        }
    }
    /* -------------------------------------------------------------------- */
    private static function __UrlGet($inType, $inId, $inUrl) {
        $return = $inUrl;
        if (empty($return)) $return = @self::$Extensions['depends'][$inType][$inId];
        if (empty($return)) $return = @self::$Extensions['recommends'][$inType][$inId];
        if (empty($return)) $return = @self::$Extensions['suggests'][$inType][$inId];
        $return = strtr($return, ['$ID$' => $inId]);
        return $return;
    }
    /* -------------------------------------------------------------------- */
    private static function __PathGet($inType, $inId) {
        return $inType == 'template' ? "lib/tpl/$inId" : "lib/plugins/$inId";
    }
    /* -------------------------------------------------------------------- */
}