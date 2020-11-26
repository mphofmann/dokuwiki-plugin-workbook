<?php
namespace workbook\wbinc\doku;
use function cleanID;
use function getNS;
use function noNS;
use function wl;
class DokuUtil {
    /* -------------------------------------------------------------------- */
    public static function NsidHiddenIs($inNsid) {
        return isHiddenPage($inNsid);
    }
    /* -------------------------------------------------------------------- */
    public static function RedirectSend($inUrl) {
        send_redirect($inUrl);
    }
    /* -------------------------------------------------------------------- */
    public static function SectokGet() {
        return getSecurityToken();
    }
    /* -------------------------------------------------------------------- */
    public static function IdCleanGet($inId) {
        return cleanID($inId);
    }
    /* -------------------------------------------------------------------- */
    public static function NsGet($inId) {
        return getNS($inId);
    }
    /* -------------------------------------------------------------------- */
    public static function PageGet($inId) {
        return noNS($inId);
    }
    /* -------------------------------------------------------------------- */
    public static function WikiLinkGet($inId = '', $inUrlParameters = '', $inAbsolute = false, $inSeparator = '&amp;') {
        return wl($inId, $inUrlParameters, $inAbsolute, $inSeparator);
    }
    /* -------------------------------------------------------------------- */
    public static function VarReplace($inString) {
        $file = self::PageGet(DokuSysGlobal::NsidGet());
        $page = self::IdCleanGet($file);
        if (substr($page, -3, 1) == '_') $page = substr($page, 0, -3);
        $page = str_replace('_', ' ', $page);
        $ar = $_SESSION[DOKU_COOKIE]['auth'] ?? ['user' => '', 'info' => ['name' => '', 'mail' => '']];
        $return = strtr($inString, [ //
            '@ID@' => DokuSysGlobal::NsidGet(), //
            '@NS@' => self::NsGet(DokuSysGlobal::NsidGet()), //
            '@FILE@' => $file, //
            '@!FILE@' => utf8_ucfirst($file), //
            '@!FILE!@' => utf8_strtoupper($file), //
            '@PAGE@' => $page, //
            '@!PAGE@' => utf8_ucfirst($page), //
            '@!!PAGE@' => utf8_ucwords($page), //
            '@!PAGE!@' => utf8_strtoupper($page), //
            '@USER@' => $ar['user'], //
            '@NAME@' => $ar['info']['name'], // doku\DokuSysGlobal::InfoGet('userinfo', 'name'), //
            '@MAIL@' => $ar['info']['mail'], // doku\DokuSysGlobal::InfoGet('userinfo', 'mail'), //
            '@DATE@' => date('Y-m-d'), //
            '@TIME@' => date('H:i'), //
            '@YEAR@' => date('Y'), //
            '@MONTH@' => date('m'), //
            '@DAY@' => date('d'), //
        ]);
        return $return;
    }
    /* -------------------------------------------------------------------- */
}