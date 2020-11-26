<?php
namespace workbook\wbinc\admincore;
use workbookcore\wbinc\base;
use workbookcore\wbinc\mod;
use workbookdata\wbinc\datatable;
use workbookcore\wbinc\xhtml;
class AdmincoreWbTable {
    /* -------------------------------------------------------------------- */
    public static function Reset($inWb, $inTable) {
        if (empty($inWb) or empty($inTable)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Missing inputs.");
        if (!mod\ModWbTable::CommandEnabledCheck($inWb, $inTable, __FUNCTION__)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Method '" . __FUNCTION__ . "' not enabled.");
        // Reset
        xhtml\XhtmlMsg::Echo('Info', __METHOD__, "$inWb $inTable", "");
        if (mod\ModWbTable::CommandEnabledCheck($inWb, $inTable, 'reschema')) {
            self::Reschema($inWb, $inTable);
        }
        xhtml\XhtmlMsg::Echo('Info', __METHOD__, "$inWb $inTable", "");
        if (mod\ModWbTable::CommandEnabledCheck($inWb, $inTable, 'reassign')) {
            self::Reassign($inWb, $inTable);
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Reschema($inWb, $inTable) {
        if (empty($inWb) or empty($inTable)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Missing inputs.");
        if (!mod\ModWbTable::CommandEnabledCheck($inWb, $inTable, __FUNCTION__)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Method '" . __FUNCTION__ . "' not enabled.");
        // Reschema
        $ar = AdmincoreWb::TableAr($inWb);
        if (!isset($ar[$inTable])) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "No table fields defined.");
        $fields = $ar[$inTable];
        xhtml\XhtmlMsg::Echo('Info', __METHOD__, "$inWb $inTable", "Fields: $fields");
        $jsonold = AdmincoreSchema::JsonExport("{$inWb}_{$inTable}");
        $jsonnew = AdmincoreSchema::JsonGenerateGet("{$inWb}_{$inTable}", $fields);
        $json = AdmincoreSchema::JsonLabelColrefRemapGet($jsonold, $jsonnew);
        AdmincoreSchema::JsonImport("{$inWb}_{$inTable}", $json);
        // check
        $arimport = json_decode($json, true);
        $arexport = json_decode(AdmincoreSchema::JsonExport("{$inWb}_{$inTable}"), true);
        $fieldsexport = (substr($inTable, 0, 4) == 'page') ? '%pageid%,' : '';
        foreach ($arexport['columns'] as $col) {
            if ($col['isenabled'] == '1') {
                $fieldsexport .= $col['label'] . ',';
            }
        }
        $fieldsexport = substr($fieldsexport, 0, -1);
        $fieldsimport = $fields;
        if (strpos($fieldsimport, '[') !== false) {
            $start = strpos($fieldsimport, '[');
            $end = strpos($fieldsimport, ']', $start + 1) + 1;
            $fieldsimport = substr($fieldsimport, 0, $start) . substr($fieldsimport, $end);
        }
        if ($fieldsexport != substr($fieldsimport, 0, strlen($fieldsexport))) { // if (substr($fieldsexport, 0, strlen($fieldsimport)) != $fieldsimport) {
            xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Schema field order not ok: Import: $fieldsimport / Export: $fieldsexport");
        }
        $diff = array_diff($arimport, $arexport);
        unset($diff['id'], $diff['user']);
        if (!empty($diff)) {
            xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Schema not updated properly.");
            xhtml\XhtmlMsg::PrintR($diff);
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Reassign($inWb, $inTable) {
        if (empty($inWb) or empty($inTable)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Missing inputs.");
        if (!mod\ModWbTable::CommandEnabledCheck($inWb, $inTable, __FUNCTION__)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Method '" . __FUNCTION__ . "' not enabled.");
        // Reassign
        xhtml\XhtmlMsg::Echo('Info', __METHOD__, "$inWb $inTable", "Reassigning.");
        if (substr($inTable, 0, 4) == 'page') {
            AdmincoreSchema::AssignmentPatternUpdate("{$inWb}_{$inTable}", "{$inWb}:datapage:" . substr($inTable, 4) . ":**");
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Recheck($inWb, $inTable) {
        if (empty($inWb) or empty($inTable)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Missing inputs.");
        if (!mod\ModWbTable::CommandEnabledCheck($inWb, $inTable, __FUNCTION__)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Method '".__FUNCTION__."' not enabled.");
        // Cleanup
        if (!base\Base::ClassExists('datatable\Datatable')) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Class Datatable not installed.");
        datatable\Datatable::Recheck($inWb,$inTable);
    }
    /* -------------------------------------------------------------------- */
    public static function Clear($inWb, $inTable) {
        if (empty($inWb) or empty($inTable)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Missing inputs.");
        if (!mod\ModWbTable::CommandEnabledCheck($inWb, $inTable, __FUNCTION__)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Method '" . __FUNCTION__ . "' not enabled.");
        // Clear
        xhtml\XhtmlMsg::Echo('Info', __METHOD__, "$inWb $inTable", "");
        xhtml\XhtmlMsg::Echo('Notice', __METHOD__, "$inWb $inTable", 'TODO - not yet implemented.');
    }
    /* -------------------------------------------------------------------- */
    public static function Reimport($inWb, $inTable) {
        if (empty($inWb) or empty($inTable)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Missing inputs.");
        if (!mod\ModWbTable::CommandEnabledCheck($inWb, $inTable, __FUNCTION__)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Method '" . __FUNCTION__ . "' not enabled.");
        xhtml\XhtmlMsg::Echo('Info', __METHOD__, "$inWb $inTable", "");
        xhtml\XhtmlMsg::Echo('Notice', __METHOD__, "$inWb $inTable", 'TODO - not yet implemented.');
    }
    /* -------------------------------------------------------------------- */
}