<?php
namespace workbook\wbincdoku\doku;
class DokuDataSqlite {
    /* -------------------------------------------------------------------- */
    public static function QueryAr($inQuery, $inDb = 'struct'): array {
        $sqlite = self::__NewObj($inDb);
        $res = $sqlite->query($inQuery);
        $rows = $sqlite->res2arr($res);
        $sqlite->res_close($res);
        return $rows ?? [];
    }
    /* -------------------------------------------------------------------- */
    public static function ArInsertObj($inTable, $inAr, $inDb = 'struct'): ?object {
        $returnobj = null;
        $sqlite = self::__NewObj($inDb);
        if (!empty($sqlite)) {
            $returnobj = $sqlite->storeEntry($inTable, $inAr);
        }
        return $returnobj;
    }
    /* -------------------------------------------------------------------- */
    private static function __NewObj($inDb): ?object {
        $returnobj = DokuPlugin::LoadObj('helper', 'sqlite');
        if (!$returnobj) {
            DokuXhtmlMsg::Echo('Error', __METHOD__, '', 'Plugin "sqlite" required.');
            return null;
        }
        if (!$returnobj->init($inDb, WB_DATAMETA)) {
            DokuXhtmlMsg::Echo('Error', __METHOD__, $inDb, 'Database not initialized.');
            return null;
        }
        return $returnobj;
    }
    /* -------------------------------------------------------------------- */
}