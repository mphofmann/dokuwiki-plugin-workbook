<?php
namespace workbook\wbinc\admincore;
use workbookcore\wbinc\base;
use workbookcore\wbinc\mod;
use workbookcore\wbinc\sys;
use workbookcore\wbinc\util;
use workbookcore\wbinc\xhtml;
class AdmincoreWb {
    /* -------------------------------------------------------------------- */
    public static function ToolAr($inWb) {
        $ar = mod\ModWb::IniAr($inWb);
        return @$ar['tools'];
    }
    /* -------------------------------------------------------------------- */
    public static function TableAr($inWb, $inType = 'database datapage') {
        $returns = [];
        $ar = mod\ModWb::IniAr($inWb);
        foreach (['database', 'datapage'] as $val) {
            if (strpos($inType, $val) !== false) {
                $returns = array_merge($returns, self::__TableAr($ar[$val]));
            }
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    public static function Install($inWb) {
        if (empty($inWb)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb", "Missing inputs.");
        if (!mod\ModWb::CommandEnabledCheck($inWb, 'install')) return xhtml\XhtmlMsg::Echo('Notice', __METHOD__, "$inWb", "Method 'install' not enabled.");
        // Install
        xhtml\XhtmlMsg::Echo('Info', __METHOD__, "$inWb", "");
        $path = "data/pages/{$inWb}";
        if (!is_dir($path)) {
            mkdir($path);
            self::Reset($inWb, 'install');
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Resets() {
        foreach (scandir('data/pages/') as $wb) {
            if (substr($wb, 0, 1) == '.') continue;
            if (!is_dir("data/pages/$wb")) continue;
            self::Reset($wb);
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Reset($inWb, $inCommand = 'reset') {
        if (empty($inWb)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb", "Missing inputs.");
        if (!mod\ModWb::CommandEnabledCheck($inWb, 'reset')) return xhtml\XhtmlMsg::Echo('Notice', __METHOD__, "$inWb", "Method 'reset' not enabled.");
        // Reset
        xhtml\XhtmlMsg::Echo('Info', __METHOD__, "$inWb", "$inCommand");
        $tools = [];
        if ($inCommand == 'reset') {
            foreach (sys\SysNs::ScandirAr($inWb, 'local', 'pages', 'dirs hidepages') as $tool) { // foreach (self::ToolAr($inWb) as $tool => $modes) {
                if (substr($tool, -1) == ':') $tool = substr($tool, 0, -1);
                $tools[] = $tool;
            }
        } else { // install
            $ar = mod\ModWb::IniAr($inWb);
            if (is_array($ar['tools'])) {
                foreach ($ar['tools'] as $id => $val) {
                    if (strpos($val, 'install') !== false) $tools[] = $id;
                }
            }
        }
        foreach ($tools as $tool) {
            if (mod\ModWbTool::CommandEnabledCheck($inWb, $tool, $inCommand)) { // if (strpos($modes, $inMode) !== false or is_dir("data/pages/$inWb/$tool")) {
                self::__IdReplace("$inWb:$tool:start", util\UtilSyntax::WbTagGet());
                AdmincoreWbTool::Reset($inWb, $tool);
            }
        }
        foreach (self::TableAr($inWb) as $table => $fields) {
            if (mod\ModTable::CommandEnabledCheck($table, 'reset')) {
                AdmincoreWbTable::Reset($inWb, $table);
            }
        }
        $ar = mod\ModWb::IniAr($inWb);
        if (is_array($ar['pages'])) {
            xhtml\XhtmlMsg::Echo('Info', __METHOD__, "$inWb", "");
            foreach ($ar['pages'] as $id => $val) {
                $id = strtr($id, ['@wb@' => $inWb]);
                self::__IdReplace($id, $val, true, ['@wb@' => $inWb, '\n' => "\n"]);
            }
        }
        AdmincoreContent::StartWbonlyExec('remove', $inWb);
    }
    /* -------------------------------------------------------------------- */
    public static function Syncclear($inWb) {
        if (empty($inWb)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb", "Missing inputs.");
        AdmincoreOperating::WbSyncExec('clear', $inWb);
        xhtml\XhtmlMsg::Echo('Info', __METHOD__, "$inWb", "Cache cleared.");
    }
    /* -------------------------------------------------------------------- */
    public static function ZArchive($inWb) {
        if (empty($inWb)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb", "Missing inputs.");
        // if (!mod\ModWbTool::CommandEnabledCheck($inWb, $inTool, __FUNCTION__)) return base\BaseMsg::Echo('Warning', __METHOD__, "$inWb $inTool", "Method '" . __FUNCTION__ . "' not enabled.");
        // ZArchive
        xhtml\XhtmlMsg::Echo('Info', __METHOD__, "$inWb", "Moving to zarchive.");
        $pathin = "data/pages/$inWb/";
        $pathout = "data/pages/zworkbook/zarchive/" . date('Y');
        $dir = date('Y-m-d-His') . "-{$inWb}";
        util\UtilPath::MkdirCheck($pathout);
        AdminCmd::SystemGet("mv {$pathin} {$pathout}{$dir}");
        self::Syncclear($inWb);
    }
    /* -------------------------------------------------------------------- */
    private static function __TableAr($inArray = []) {
        $returns = [];
        if (!is_array($inArray)) return [];
        foreach (@$inArray as $id => $ar) {
            if (is_array($ar)) {
                foreach ($ar as $id2 => $val2) {
                    $returns[base\BaseAuth::AclStrip($id2)] = $val2;
                }
            } else {
                $returns[base\BaseAuth::AclStrip($id)] = $ar;
            }
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    private static function __IdReplace($inId, $inContent, $doCheck = true, $arReplace = []) {
        $id = strtr($inId, $arReplace);
        $content = strtr($inContent, $arReplace);
        $rc = sys\SysNsid::ContentsPut($id, 'local', $content, $doCheck);
        $msg = ($rc) ? 'Page generated' : 'Page modified ... skipping';
        xhtml\XhtmlMsg::Echo('Info', __METHOD__, "$inId", $msg);
    }
    /* -------------------------------------------------------------------- */
}