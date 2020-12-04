<?php
namespace workbook\wbinc\doku;
use function ft_mediause;
class DokuMedia {
    /* -------------------------------------------------------------------- */
    public static function BacklinkAr($inNsmedia) {
        $returns = ft_mediause($inNsmedia);
        return is_array($returns) ? $returns : [];
    }
    /* -------------------------------------------------------------------- */
}