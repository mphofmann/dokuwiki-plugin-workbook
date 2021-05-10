<?php
namespace workbook\wbincdoku\doku;
class DokuXhtml {
    /* -------------------------------------------------------------------- */
    public static function IndexerGet(): string { // provide DokuWiki housekeeping, required in all templates
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
    public static function SectokGet(): string {
        return getSecurityToken();
    }
    /* -------------------------------------------------------------------- */
    public static function SecTokenCheck(): bool {
        return checkSecurityToken();
    }
    /* -------------------------------------------------------------------- */
    public static function SecTokenEcho(): void {
        echo formSecurityToken(false);
    }
    /* -------------------------------------------------------------------- */
    public static function IdCleanGet($inId): string {
        return cleanID($inId);
    }
    /* -------------------------------------------------------------------- */
    public static function WikiLinkGet($inId = '', $inUrlParameters = '', $inAbsolute = false, $inSeparator = '&amp;'): string {
        return wl($inId, $inUrlParameters, $inAbsolute, $inSeparator);
    }
    /* -------------------------------------------------------------------- */
}