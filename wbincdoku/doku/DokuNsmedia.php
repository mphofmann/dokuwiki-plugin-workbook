<?php
namespace workbook\wbincdoku\doku;
use function ft_mediause;
class DokuNsmedia {
    /* -------------------------------------------------------------------- */
    public static function BacklinkAr($inNsmedia): array {
        $returns = ft_mediause($inNsmedia);
        return is_array($returns) ? $returns : [];
    }
    /* -------------------------------------------------------------------- */
}