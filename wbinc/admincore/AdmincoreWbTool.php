<?php
namespace workbook\wbinc\admincore;
use workbook\wbinc\admin;
use workbookcore\wbinc\env;
use workbookcore\wbinc\mod;
use workbookcore\wbinc\sys;
use workbookcore\wbinc\util;
use workbookcore\wbinc\xhtml;
use workbookuser\wbinc\user;
class AdmincoreWbTool {
    /* -------------------------------------------------------------------- */
    public static function Exists($inWb, $inTool) {
        return (is_dir("data/pages/$inWb/$inTool"));
    }
    /* -------------------------------------------------------------------- */
    public static function Reset($inWb, $inTool) {
        if (empty($inWb) or empty($inTool)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb:$inTool", "Missing inputs.");
        if (!mod\ModWbTool::CommandEnabledCheck($inWb, $inTool, __FUNCTION__)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb:$inTool", "Method '" . __FUNCTION__ . "' not enabled.");
        xhtml\XhtmlMsg::Echo('Info', __METHOD__, "$inWb:$inTool", "");
        if (mod\ModWbTool::CommandEnabledCheck($inWb, $inTool, 'repage')) {
            self::Repage($inWb, $inTool);
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Syncclear($inWb, $inTool) {
        if (empty($inWb) or empty($inTool)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb:$inTool", "Missing inputs.");
        AdmincoreOperating::WbSyncExec('clear', "$inWb:$inTool");
        xhtml\XhtmlMsg::Echo('Info', __METHOD__, "$inWb:$inTool", "Sync cleared.");
    }
    /* -------------------------------------------------------------------- */
    public static function Repage($inWb, $inTool) {
        if (empty($inWb) or empty($inTool)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb:$inTool", "Missing inputs.");
        if (!mod\ModWbTool::CommandEnabledCheck($inWb, $inTool, __FUNCTION__)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb:$inTool", "Method '" . __FUNCTION__ . "' not enabled.");
        xhtml\XhtmlMsg::Echo('Info', __METHOD__, "$inWb:$inTool", "");
        self::__PagesGenerate($inWb, $inTool);
        self::__PagesAdd($inWb, $inTool);
        self::__PagesStartWbonlyRemove($inWb, $inTool);
    }
    /* -------------------------------------------------------------------- */
    public static function Resync($inWb, $inTool) {
        if (empty($inWb) or empty($inTool)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb:$inTool", "Missing inputs.");
        if (!mod\ModWbTool::CommandEnabledCheck($inWb, $inTool, __FUNCTION__)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb:$inTool", "Method '" . __FUNCTION__ . "' not enabled.");
        xhtml\XhtmlMsg::Echo('Info', __METHOD__, "$inWb:$inTool", "");
        if (!sys\SysRemote::EnabledCheck('login')) {
            xhtml\XhtmlMsg::Echo('Warning', __METHOD__, '', 'Marketplace login failed.');
        } elseif (sys\SysRemote::NsAuthGet(mod\ModWb::TypeGet($inWb) . ":" . mod\ModTool::TypeGet($inTool)) < 1) {
            $acl = sys\SysRemote::NsAuthGet(mod\ModWb::TypeGet($inWb) . ":" . mod\ModTool::TypeGet($inTool));
            xhtml\XhtmlMsg::Echo('Notice', __METHOD__, "$inWb:$inTool", "ACL check failed. [$acl]");
        } else {
            sys\SysRemote::NsRCopy(mod\ModWb::TypeGet($inWb) . ":" . mod\ModTool::TypeGet($inTool), 'pages'); // OLD: sys\SysRemote::NsCopy(mod\ModWb::TypeGet($inWb) . ":" . mod\ModTool::TypeGet($inTool), 'syncsource', 'pages', "$inWb:$inTool"); // TODO
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Clear($inWb, $inTool) {
        if (empty($inWb) or empty($inTool)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb:$inTool", "Missing inputs.");
        if (!mod\ModWbTool::CommandEnabledCheck($inWb, $inTool, __FUNCTION__)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb:$inTool", "Method '" . __FUNCTION__ . "' not enabled.");
        if ($inTool != 'ztrash') return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb:$inTool", "Only ZTrash can be cleared.");
        xhtml\XhtmlMsg::Echo('Info', __METHOD__, "$inWb:$inTool", '');
        $pathtarget = "data/pages/{$inWb}/{$inTool}/";
        if (is_dir($pathtarget)) {
            $out = admin\AdminCmd::SystemGet("rm -R {$pathtarget}*");
            if (!empty($out)) echo $out;
        }
    }
    /* -------------------------------------------------------------------- */
    public static function ZArchive($inWb, $inTool) {
        return AdmincoreNs::ZArchive("$inWb:$inTool");
    }
    /* -------------------------------------------------------------------- */
    public static function ZTrash($inWb, $inTool) {
        return AdmincoreNs::ZTrash("$inWb:$inTool");
    }
    /* -------------------------------------------------------------------- */
    private static function __PagesGenerate($inWb, $inTool) {
        $ar = mod\ModTool::IniAr($inTool);
        self::__IdReplace("$inWb:$inTool:start", util\UtilSyntax::WbTagGet());
        if (is_array($ar['pages'])) {
            xhtml\XhtmlMsg::Echo('Info', __METHOD__, "$inWb:$inTool", "");
            foreach ($ar['pages'] as $id => $val) {
                self::__IdReplace($id, $val, true, ['@wb@' => $inWb, '@tool@' => $inTool]);
            }
        }
    }
    /* -------------------------------------------------------------------- */
    private static function __PagesAdd($inWb, $inTool) {
        switch ($inTool) {
            case 'database':
            case 'datapage':
                $ar = mod\ModWb::IniAr($inWb);
                foreach (@$ar[$inTool] as $id => $val) {
                    if (is_array($val)) {
                        foreach ($val as $id2 => $val) {
                            self::__PagesIdReplace("$inWb:$inTool:$id",$id2,$inTool);

//                            $id2 = ($inTool == 'datapage' and substr($id2, 0, 4) == 'page') ? substr($id2, 4) : $id2;
//                            self::__IdReplace("$inWb:$inTool:$id:$id2:start", util\UtilSyntax::WbTagGet());
                        }
                    } else {
                        self::__PagesIdReplace("$inWb:$inTool",$id,$inTool);

//                        $id = ($inTool == 'datapage' and substr($id, 0, 4) == 'page') ? substr($id, 4) : $id;
//                        self::__IdReplace("$inWb:$inTool:$id:start", util\UtilSyntax::WbTagGet());
                    }
                }
                break;
            case 'uwall':
            case 'uprivate':
                foreach (env\EnvUser::Ars() as $id => $ar) {
                    if (class_exists('user\UserAdmin')) {
                        user\UserAdmin::WbToolReset($id, $inWb, $inTool);
                    }
                }
                break;
        }
    }
    /* -------------------------------------------------------------------- */
    private static function __PagesIdReplace($inNs, $inTable, $inTool) {
        $id = ($inTool == 'datapage' and substr($inTable, 0, 4) == 'page') ? substr($inTable, 4) : $inTable;
        $nsid = $inTool=='datapage' ? "$inNs:$id:start" : "$inNs:$id";
        self::__IdReplace($nsid, util\UtilSyntax::WbTagGet());
    }
    /* -------------------------------------------------------------------- */
    private static function __PagesStartWbonlyRemove($inWb, $inTool) {
        AdmincoreContent::StartWbonlyExec('remove', "$inWb:$inTool");
    }
    /* -------------------------------------------------------------------- */
    private static function __IdReplace($inId, $inContent, $doCheck = true, $arReplace = []) {
        $id = util\UtilSyntax::VarReplace($inId, $arReplace);
        $content = util\UtilSyntax::VarReplace($inContent, $arReplace);
        $rc = sys\SysNsid::ContentsPut($id, 'local', $content, $doCheck);
        $msg = ($rc) ? 'Page generated' : 'Page modified ... skipping';
        xhtml\XhtmlMsg::Echo('Info', __METHOD__, "$inId", $msg);
    }

    /* DEPRECATED --------------------------------------------------------- */
    /* public static function Remedia($inWb, $inTool) {
        if (empty($inWb) or empty($inTool)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb:$inTool", "Missing inputs.");
        if (!mod\ModWbTool::CommandEnabledCheck($inWb, $inTool, __FUNCTION__)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb:$inTool", "Method '" . __FUNCTION__ . "' not enabled.");
        // Remedia
        xhtml\XhtmlMsg::Echo('Info', __METHOD__, "$inWb:$inTool", "");
        self::__DataCopy('media', $inWb, $inTool);
    }
    private static function __DataCopy($inType, $inWb, $inTool) {
        $pathsource = "data/pages/wb/wb/" . base\Base::VersionGet() . "data/$inType/$inWb/$inTool/";
        if (is_dir($pathsource)) {
            xhtml\XhtmlMsg::Echo('Info', __METHOD__, "$inWb:$inTool", "");
            $pathtarget = "data/$inType/$inWb/$inTool/";
           AdminCmd::SystemEcho("cp -R {$pathsource}* {$pathtarget}");
        }
    }
    /* -------------------------------------------------------------------- */
}