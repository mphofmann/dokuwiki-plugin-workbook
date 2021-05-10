<?php
namespace workbook\wbincdoku\doku;
use function tpl_content;
class DokuAreaBody {
    /* -------------------------------------------------------------------- */
    public static function AreaGet(): string {
        ob_start();
        tpl_content();
        return ob_get_clean();
    }
    /* -------------------------------------------------------------------- */
}