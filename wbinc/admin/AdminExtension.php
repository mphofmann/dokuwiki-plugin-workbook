<?php
namespace workbook\wbinc\admin;
class AdminExtension {
    /* -------------------------------------------------------------------- */
    public static function RowAr($inGroup, $inAr, $inExttype, $inAttr = ''): array {
        $returns = [];
        foreach ($inAr as $id => $ar) {
            $strstatus = $inAttr == 'disabled' ? AdminXhtml::StatusGet('white') : AdminXhtml::ButtonGet("admin\AdminExtension::Exec action=info type=$inExttype id=$id", AdminCmd::ExecGet("admin\AdminExtension::Exec action=status type=$inExttype id=$id"));
            $cmd = is_dir(self::__PathGet($inExttype, $id)) ? 'Replace' : 'Install';
            $strexec = AdminXhtml::ButtonGet("admin\AdminExtension::Exec action=" . strtolower($cmd) . " type=$inExttype id=$id", "[$cmd]", $inAttr);
            if ($inGroup != 'depends' and $cmd == 'Replace') $strexec .= AdminXhtml::ButtonGet("admin\AdminExtension::Exec action=remove type=$inExttype id=$id", "[Remove]", $inAttr, 'xsmall', 'OK?');
            $strlink = $inExttype == 'module' ? '' : AdminXhtml::LinkGet("doku.php?do=admin&page=extension&tab=$inExttype");
            $returns[] = [$id, AdminCmd::ExecGet("admin\AdminExtension::Exec action=note type=$inExttype id=$id"), $strstatus, $strexec, $strlink];
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    public static function Exec($inAction, $inType, $inId) {
        $return = '';
        switch ($inAction) {
            case 'note':
                $return .= self::NoteGet($inType, $inId);
                break;
            case 'info':
                $return .= self::__InfoGet($inType, $inId);
                break;
            case 'install':
                $extpath = self::__PathGet($inType, $inId);
                if ( ! empty($extpath) and is_dir($extpath)) {
                    AdminXhtmlMsg::Echo('Warning', '', '', "Extension already installed: $inAction $inType $inId");
                } else {
                    $return .= self::Replace($inType, $inId);
                }
                break;
            case 'replace':
                $return .= self::Replace($inType, $inId);
                break;
            case 'remove':
                self::__Remove($inType, $inId);
                break;
            case 'status':
                $color = 'red';
                $title = 'Not installed';
                $extpath = self::__PathGet($inType, $inId);
                if ( ! empty($extpath) and is_dir($extpath)) {
                    $extmtime = filemtime($extpath);
                    $color = 'green';
                    $title = "Installed: " . date('Y-m-d H:i:s', $extmtime);
                    if ( ! empty($inUrl)) {
                        $src = self::__SrcGet('src', $inType, $inId);
                        $srcmtime = AdminRemote::UrlMtime($src);
                        if ($extmtime < $srcmtime) {
                            $color = 'yellow';
                            $title .= " / Updateable: " . date('Y-m-d H:i:s', $srcmtime);
                        }
                    }
                }
                $return .= AdminXhtml::StatusGet($color, $title);
                break;
            default:
                AdminXhtmlMsg::Echo('Warning', '', '', "Extension action unknown: $inAction $inType $inId");
                break;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function NoteGet($inType, $inId): string {
        $return = '';
        $systems = AdminRemote::ExtensionAr($inType, $inId);
        $return .= @$systems['note'];
        $ar = [ //
            'workbook' => '<span title="Michael P. Hofmann AG, Rapperswil, Switzerland">by Manageopedia</span>', '.deb' => '<span title="Michael P. Hofmann AG, Rapperswil, Switzerland">by Manageopedia</span>', 'deb:' => '<span title="Michael P. Hofmann AG, Rapperswil, Switzerland">by Manageopedia</span>', //
            'splitbrain' => '<span title="Andrea Gohr, Berlin, Germany">by Splitbrain</span>', //
            'cosmocode' => '<span title="CosmoCode GmbH, Berlin, Germany">by CosmoCode</span>', //
            'michitux' => '<span title="Michael Hamann, Karlsruhe, Germany">by Hamann</span>', //
        ];
        $url = self::__SrcGet('src', $inType, $inId);
        foreach ($ar as $id => $val) {
            if (strpos($url, $id) !== false) {
                $return .= " ($val)";
                break;
            }
        }
        if (empty($return)) $return .= $url;
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function Replace($inType, $inId): bool {
        $src = self::__SrcGet('src', $inType, $inId);
        if (empty($src)) return AdminXhtmlMsg::EchoFalse('Warning', '', '', "Source is missing: $inType-$inId");
        $path = self::__SrcGet('path', $inType, $inId);
        if (substr($src, -4) == '.deb' and empty($path)) return AdminXhtmlMsg::EchoFalse('Warning', '', '', "Path is missing: $inType-$inId");
        // download
        $filepath = self::__SrcDownloadFilepathGet($src);
        if (empty($filepath)) return AdminXhtmlMsg::EchoFalse('Warning', '', '', "Download failed: $inType-$inId");
        $basename = basename($filepath);
        // copy to tmp
        $dirpath = 'data/tmp/dir' . uniqid() . '/';
        AdminInode::MkdirCheck($dirpath);
        copy($filepath, $dirpath . $basename);
        // extract
        switch (substr($src, -4)) {
            case '.zip':
                $zip = new \ZipArchive;
                $res = $zip->open($dirpath . $basename);
                if ($res === false) return AdminXhtmlMsg::EchoFalse('Warning', '', '', "Extracting failed: $inType-$inId");
                $zip->extractTo($dirpath);
                $zip->close();
                unlink($dirpath . $basename);
                AdminXhtmlMsg::Echo('Info', '', '', "Extension new extracted: $inType-$inId");
                break;
            case '.deb':
                system("cd $dirpath; ar -x $basename data.tar.xz 2>&1");
                system("cd $dirpath; tar -xf data.tar.xz .{$path} 2>&1");
                unlink($dirpath . $basename);
                unlink($dirpath . 'data.tar.xz');
                AdminXhtmlMsg::Echo('Info', '', '', "Extension new extracted: $inType-$inId");
                break;
        }
        // move
        $extpath = self::__PathGet($inType, $inId);
        switch (substr($src, -4)) {
            case '.zip':
                foreach (scandir($dirpath) as $inode) {
                    if (substr($inode, 0, 1) == '.') continue;
                    self::__Remove($inType, $inId);
                    AdminXhtmlMsg::Echo('Info', '', '', "Extension old removed: $inType-$inId");
                    system("mv {$dirpath}{$inode} {$extpath} 2>&1");
                    touch($extpath);
                    AdminXhtmlMsg::Echo('Info', '', '', "Extension new installed: $inType-$inId");
                    break;
                }
                break;
            case '.deb':
                self::__Remove($inType, $inId);
                AdminXhtmlMsg::Echo('Info', '', '', "Extension old removed: $inType-$inId");
                system("mv {$dirpath}{$path} {$extpath} 2>&1");
                touch($extpath);
                AdminXhtmlMsg::Echo('Info', '', '', "Extension new installed: $inType-$inId");
                break;
        }
        // cleanup
        system("rm -R $dirpath 2>&1");
        // AdminCache::ConfLocalTouch('css&js');
        AdminXhtmlMsg::Echo('Success', '', '', "Extension installed: $inType-$inId");
        AdminXhtmlMsg::Echo('Notice', '', '', 'Purge the cache once your are done to refresh CSS and JS.');
        return true;
    }
    /* -------------------------------------------------------------------- */
    private static function __Remove($inType, $inId): bool {
        $extpath = self::__PathGet($inType, $inId);
        AdminInode::RmR($extpath);
        return true;
    }
    /* -------------------------------------------------------------------- */
    private static function __SrcGet($inField, $inExttype, $inId): string {
        $return = '';
        $ar = AdminRemote::SystemsAr();
        if (empty($return)) $return = @$ar["depends-$inExttype"][$inId][$inField];
        if (empty($return)) $return = @$ar["recommends-$inExttype"][$inId][$inField];
        if (empty($return)) $return = @$ar["suggests-$inExttype"][$inId][$inField];
        $artr = array_merge($ar['*'], ['@ID@' => $inId, '@EXTTYPE@' => $inExttype, '@DEBDIST@' => AdminConf::Get('plugin', 'workbook', 'connect_dist')]);
        $return = strtr($return, $artr);
        return $return;
    }
    /* -------------------------------------------------------------------- */
    private static function __SrcDownloadFilepathGet($inSrc): string {
        $basename = basename($inSrc);
        if ( ! AdminCache::Exists(__NAMESPACE__, 'download', $basename)) {
            $str = file_get_contents($inSrc);
            if (strlen($str) > 0) {
                AdminCache::Put(__NAMESPACE__, 'download', $basename, $str);
                AdminXhtmlMsg::Echo('Info', '', '', "Source downloaded: $inSrc");
            }
        }
        $filepath = AdminCache::FilepathGet(__NAMESPACE__, 'download', $basename);
        return file_exists($filepath) ? $filepath : '';
    }
    /* -------------------------------------------------------------------- */
    private static function __PathGet($inType, $inId): string {
        $ar = ['module' => "workbook/module/$inId", 'plugin' => "lib/plugins/$inId", 'template' => "lib/tpl/$inId"];
        return $ar[$inType];
    }
    /* -------------------------------------------------------------------- */
    private static function __InfoGet($inType, $inId): string {
        $return = '';
        $return .= "[Info $inType $inId] not implemented yet"; // TODO
        return $return;
    }
    /* -------------------------------------------------------------------- */
}