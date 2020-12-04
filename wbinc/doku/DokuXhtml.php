<?php
namespace workbook\wbinc\doku;
use function tpl_content;
use function tpl_getMediaFile;
use function tpl_include_page;
use function tpl_indexerWebBug;
use function tpl_metaheaders;
use function tpl_searchform;
class DokuXhtml {
    /* -------------------------------------------------------------------- */
    public static function MetaheadersGet() {
        ob_start();
        tpl_metaheaders();
        return ob_get_clean();
    }
    /* -------------------------------------------------------------------- */
    public static function WidgetFormSearchGet() {
        ob_start();
        tpl_searchform();
        return ob_get_clean();
    }
    /* -------------------------------------------------------------------- */
    public static function BodyGet() {
        ob_start();
        tpl_content();
        return ob_get_clean();
    }
    /* -------------------------------------------------------------------- */
    public static function TplPageIncludeGet($inNsid) {
        return tpl_include_page($inNsid, false);
    }
    /* -------------------------------------------------------------------- */
    public static function MediaSrcGet($inMediaidAr) {
        return tpl_getMediaFile($inMediaidAr);
    }
    /* -------------------------------------------------------------------- */
    public static function SectokGet() {
        return getSecurityToken();
    }
    /* -------------------------------------------------------------------- */
    public static function DokuIndexerGet() { // provide DokuWiki housekeeping, required in all templates
        $return = '';
        $return .= '<div class="no">';
        ob_start();
        tpl_indexerWebBug();
        $return .= ob_get_clean();
        $return .= '</div>';
        $return .= '<div id="screen__mode" class="no"></div>';
        return $return;
    }
    /* -------------------------------------------------------------------- */
}