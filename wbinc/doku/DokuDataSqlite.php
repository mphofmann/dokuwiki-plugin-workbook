<?php
namespace workbook\wbinc\doku;
class DokuDataSqlite {
    /* -------------------------------------------------------------------- */
    public static function Query($inQuery, $inDb = 'struct') {
        $sqlite = self::__ObjNew($inDb);
        $res = $sqlite->query($inQuery);
        $rows = $sqlite->res2arr($res);
        $sqlite->res_close($res);
        return $rows;
    }
    /* -------------------------------------------------------------------- */
    public static function ArInsert($inTable, $inAr, $inDb = 'struct') {
        $return = '';
        $sqlite = self::__ObjNew($inDb);
        if ($sqlite !== false) {
            $return = $sqlite->storeEntry($inTable, $inAr);
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    private static function __ObjNew($inDb) {
        $sqlite = DokuPlugin::Load('helper', 'sqlite');
        if (!$sqlite) {
            DokuXhtmlMsg::Echo('Error', __METHOD__, '', 'Plugin "sqlite" required.');
            return false;
        }
        if (!$sqlite->init($inDb, 'data/meta/')) {
            DokuXhtmlMsg::Echo('Error', __METHOD__, $inDb, 'Database not initialized.');
            return false;
        }
        return $sqlite;
    }
    /* -------------------------------------------------------------------- */
}