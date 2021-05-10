<?php
namespace workbook\wbincdoku\doku;
use function p_set_metadata;
class DokuNsid {
    /* -------------------------------------------------------------------- */
    public static function MetaSet($inNsid, $inMetas): bool {
        p_set_metadata($inNsid, $inMetas);
        return true;
    }
    /* -------------------------------------------------------------------- */
}