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
}