<?php
namespace workbook\wbincdoku\doku;
use function p_get_instructions;
use function p_render;
class DokuRender {
    /* -------------------------------------------------------------------- */
    private static $__MapWb = ['<wb/>' => '', '<wb />' => '', '{{wb}}' => '',];
    /* -------------------------------------------------------------------- */
    public static function XhtmlGet($inMarkup, &$outInfo = [], $inDateAt = ''): string {
        DokuXhtmlMsg::Add('Debug-Notice', __METHOD__, '', $inMarkup);
        $return = '';
        $string = trim(strtr($inMarkup, self::$__MapWb)); // otherwise perpetual loop
        if (!empty($string)) {
            $return .= p_render('xhtml', p_get_instructions($string), $outInfo, $inDateAt);
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    /* public static function WikiXhtmlGet($inId, $inRev = '', $inExcuse = true, $inDateAt = ''): string {
        doku\DokuXhtmlMsg::Add('Debug-Notice', __METHOD__, $inId);
        $return = '';
        if (util\UtilSyntax::WbTagCheck(sys\SysNsid::ContentsGet($inId)) === false) { // otherwise perpetual loop
            $return = \p_wiki_xhtml($inId, $inRev, $inExcuse, $inDateAt);
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    /* private static function __p_wiki_xhtml($id, $rev = '', $excuse = true, $date_at = ''): string {
        $file = wikiFN($id, $rev);
        $ret = '';
        //ensure $id is in global $ID (needed for parsing)
        global $ID;
        $keep = $ID;
        $ID = $id;
        if ($rev || $date_at) {
            if (file_exists($file)) {
                $ret = p_render('xhtml', p_get_instructions(io_readWikiPage($file, $id, $rev)), $info, $date_at); //no caching on old revisions
            } elseif ($excuse) {
                $ret = p_locale_xhtml('norev');
            }
        } else {
            if (file_exists($file)) {
                $ret = p_cached_output($file, 'xhtml', $id);
            } elseif ($excuse) {
                $ret = p_locale_xhtml('newpage');
            }
        }
        //restore ID (just in case)
        $ID = $keep;
        return $ret;
    } */
    /* -------------------------------------------------------------------- */
}