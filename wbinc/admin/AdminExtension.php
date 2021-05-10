<?php
namespace workbook\wbinc\admin;
class AdminExtension {
    /* -------------------------------------------------------------------- */
    public static function RowAr($inGroup, $inAr, $inExttype, $inDebSrc, $inDebDist, $inDebPrefix, $inAttr = ''): array {
        $returns = [];
        foreach ($inAr as $id => $ar) {
            $src = strtr($ar['src'], ['$ID$' => $id]);
            if (substr($src, 0, 4) == 'deb:') {
                list($ext, $package, $tarpath) = explode(':', $src);
                $url = "{$inDebSrc}/{$inDebDist}/{$inDebPrefix}{$package}.{$ext}";
            } else {
                $url = $src;
                $tarpath = '';
            }
            $strstatus = $inAttr == 'disabled' ? AdminXhtml::StatusGet('white') : AdminXhtml::ButtonGet("admin\AdminExtension::Exec action=info type=$inExttype id=$id", AdminCmd::ExecGet("admin\AdminExtension::Exec action=status type=$inExttype id=$id"));
            $cmd = is_dir(self::__PathGet($inExttype, $id)) ? 'Replace' : 'Install';
            $strexec = AdminXhtml::ButtonGet("admin\AdminExtension::Exec action=" . strtolower($cmd) . " type=$inExttype id=$id url=$url tarpath=$tarpath", "[$cmd]", $inAttr);
            if ($inGroup != 'depends' and $cmd == 'Replace') $strexec .= AdminXhtml::ButtonGet("admin\AdminExtension::Exec action=remove type=$inExttype id=$id url=$url tarpath=$tarpath", "[Remove]", $inAttr, 'xsmall', 'OK?');
            $strlink = $inExttype == 'module' ? '' : AdminXhtml::LinkGet("doku.php?do=admin&page=extension&tab=$inExttype");
            $returns[] = [$id, AdminCmd::ExecGet("admin\AdminExtension::Exec action=note type=$inExttype id=$id url=$src"), $strstatus, $strexec, $strlink];
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    public static function Exec($inAction, $inType, $inId, $inUrl = '', $inTarpath = '') {
        $return = '';
        switch ($inAction) {
            case 'note':
                $return .= self::NoteGet($inType, $inId, $inUrl);
                break;
            case 'info':
                $return .= self::__InfoGet($inType, $inId);
                break;
            case 'install':
                $extpath = self::__PathGet($inType, $inId);
                if ( ! empty($extpath) and is_dir($extpath)) {
                    AdminXhtmlMsg::Echo('Warning', '', '', "Extension already installed: $inAction $inType $inId");
                } else {
                    $return .= self::Replace($inType, $inId, $inUrl, $inTarpath);
                }
                break;
            case 'replace':
                $return .= self::Replace($inType, $inId, $inUrl, $inTarpath);
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
                        $url = self::__UrlGet($inType, $inId, $inUrl);
                        $urlmtime = AdminRemote::UrlMtime($url);
                        if ($extmtime < $urlmtime) {
                            $color = 'yellow';
                            $title .= " / Updateable: " . date('Y-m-d H:i:s', $urlmtime);
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
    public static function NoteGet($inType, $inId, $inUrl = ''): string {
        $return = '';
        $systems = AdminRemote::ExtensionAr($inType, $inId);
        $return .= @$systems['note'];
        $ar = [ //
            'workbook' => '<span title="Michael P. Hofmann AG, Rapperswil, Switzerland">by Manageopedia</span>', '.deb' => '<span title="Michael P. Hofmann AG, Rapperswil, Switzerland">by Manageopedia</span>', 'deb:' => '<span title="Michael P. Hofmann AG, Rapperswil, Switzerland">by Manageopedia</span>', //
            'splitbrain' => '<span title="Andrea Gohr, Berlin, Germany">by Splitbrain</span>', //
            'cosmocode' => '<span title="CosmoCode GmbH, Berlin, Germany">by CosmoCode</span>', //
            'michitux' => '<span title="Michael Hamann, Karlsruhe, Germany">by Hamann</span>', //
        ];
        $url = self::__UrlGet($inType, $inId, $inUrl);
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
    public static function Replace($inType, $inId, $inUrl = '', $inTarpath = ''): bool {
        $url = self::__UrlGet($inType, $inId, $inUrl);
        if (empty($url)) return AdminXhtmlMsg::EchoFalse('Warning', '', '', "Url missing: $inType-$inId");
        if (substr($url, -4) == '.deb' and empty($inTarpath)) return AdminXhtmlMsg::EchoFalse('Warning', '', '', "Tarpath is empty: $inType-$inId");
        // download
        $filepath = self::__UrlDownloadFilepathGet($url);
        if (empty($filepath)) return AdminXhtmlMsg::EchoFalse('Warning', '', '', "Download failed: $inType-$inId");
        $basename = basename($filepath);
        // copy to tmp
        $dirpath = 'data/tmp/dir' . uniqid() . '/';
        AdminInode::MkdirCheck($dirpath);
        copy($filepath, $dirpath . $basename);
        // extract
        switch (substr($url, -4)) {
            case '.zip':
                $zip = new \ZipArchive;
                $res = $zip->open($dirpath . $basename);
                if ($res === false) return AdminXhtmlMsg::EchoFalse('Warning', '', '', "Extracting failed: $inType-$inId");
                $zip->extractTo($dirpath);
                $zip->close();
                unlink($dirpath . $basename);
                AdminXhtmlMsg::Echo('Info', '', '', "Extension extracted: $inType-$inId");
                break;
            case '.deb':
                system("cd $dirpath; ar -x download data.tar.xz 2>&1");
                system("cd $dirpath; tar -xf data.tar.xz .{$inTarpath} 2>&1");
                unlink($dirpath . $basename);
                unlink($dirpath . 'data.tar.xz');
                AdminXhtmlMsg::Echo('Info', '', '', "Extension extracted: $inType-$inId");
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
                    AdminXhtmlMsg::Echo('Info', '', '', "Extension copied: $inType-$inId");
                    break;
                }
                break;
            case '.deb':
                self::__Remove($inType, $inId);
                system("mv {$dirpath}{$inTarpath} {$extpath} 2>&1");
                touch($extpath);
                AdminXhtmlMsg::Echo('Info', '', '', "Extension copied: $inType-$inId");
                break;
        }
        // cleanup
        system("rm -R $dirpath 2>&1");
        AdminCmd::SystemEcho('touch ' . WB_DATACONF . 'local.php'); // TODO only styles & scripts
        AdminXhtmlMsg::Echo('Success', '', '', "Extension installed: $inType-$inId");
        return true;
    }
    /* -------------------------------------------------------------------- */
    private static function __Remove($inType, $inId): bool {
        $extpath = self::__PathGet($inType, $inId);
        if (is_dir($extpath)) {
            AdminInode::RmR($extpath);
            AdminXhtmlMsg::Echo('Info', '', '', "Extension removed: $inType-$inId");
        }
        return true;
    }
    /* -------------------------------------------------------------------- */
    private static function __UrlGet($inType, $inId, $inUrl): string {
        $return = $inUrl;
        $ar = AdminRemote::SystemsAr();
        if (empty($return)) $return = @$ar["depends-$inType"][$inId]['src'];
        if (empty($return)) $return = @$ar["recommends-$inType"][$inId]['src'];
        if (empty($return)) $return = @$ar["suggests-$inType"][$inId]['src'];
        $return = strtr($return, ['$ID$' => $inId]);
        return $return;
    }
    /* -------------------------------------------------------------------- */
    private static function __UrlDownloadFilepathGet($inUrl): string {
        $basename = basename($inUrl);
        if ( ! AdminCache::Exists(__NAMESPACE__, 'download', $basename)) {
            $str = file_get_contents($inUrl);
            if (strlen($str) > 0) {
                AdminCache::Put(__NAMESPACE__, 'download', $basename, $str);
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