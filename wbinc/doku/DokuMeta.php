<?php
namespace workbook\wbinc\doku;
use function p_set_metadata;
class DokuMeta {
    /* -------------------------------------------------------------------- */
    public static function DataSet($inNsid, $inMetas) {
        p_set_metadata($inNsid, $inMetas);
    }
    /* -------------------------------------------------------------------- */
}