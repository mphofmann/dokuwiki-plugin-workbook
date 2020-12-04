<?php
namespace workbook\wbinc\admincore;
use workbookcore\wbinc\base;
use workbookcore\wbinc\mod;
use workbookdata\wbinc\datatable;
class AdmincoreWbTable {
    /* -------------------------------------------------------------------- */
    public static function Reset($inWb, $inTable) {
        if (empty($inWb) or empty($inTable)) return base\BaseXhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Missing inputs.");
        if (!mod\ModWbTable::CommandEnabledCheck($inWb, $inTable, __FUNCTION__)) return base\BaseXhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Method '" . __FUNCTION__ . "' not enabled.");
        // Reset
        base\BaseXhtmlMsg::Echo('Info', __METHOD__, "$inWb $inTable", "");
        if (mod\ModWbTable::CommandEnabledCheck($inWb, $inTable, 'reschema')) {
            self::Reschema($inWb, $inTable);
        }
        base\BaseXhtmlMsg::Echo('Info', __METHOD__, "$inWb $inTable", "");
        if (mod\ModWbTable::CommandEnabledCheck($inWb, $inTable, 'reassign')) {
            self::Reassign($inWb, $inTable);
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Reschema($inWb, $inTable) {
        if (empty($inWb) or empty($inTable)) return base\BaseXhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Missing inputs.");
        if (!mod\ModWbTable::CommandEnabledCheck($inWb, $inTable, __FUNCTION__)) return base\BaseXhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Method '" . __FUNCTION__ . "' not enabled.");
        // Reschema
        $ar = AdmincoreWb::TableAr($inWb);
        if (!isset($ar[$inTable])) return base\BaseXhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "No table fields defined.");
        $fields = $ar[$inTable];
        base\BaseXhtmlMsg::Echo('Info', __METHOD__, "$inWb $inTable", "Fields: $fields");
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
            base\BaseXhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Schema field order not ok: Import: $fieldsimport / Export: $fieldsexport");
        }
        $diff = array_diff($arimport, $arexport);
        unset($diff['id'], $diff['user']);
        if (!empty($diff)) {
            base\BaseXhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Schema not updated properly.");
            base\BaseXhtmlMsg::PrintR($diff);
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Reassign($inWb, $inTable) {
        if (empty($inWb) or empty($inTable)) return base\BaseXhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Missing inputs.");
        if (!mod\ModWbTable::CommandEnabledCheck($inWb, $inTable, __FUNCTION__)) return base\BaseXhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Method '" . __FUNCTION__ . "' not enabled.");
        // Reassign
        base\BaseXhtmlMsg::Echo('Info', __METHOD__, "$inWb $inTable", "Reassigning.");
        if (substr($inTable, 0, 4) == 'page') {
            AdmincoreSchema::AssignmentPatternUpdate("{$inWb}_{$inTable}", "{$inWb}:datapage:" . substr($inTable, 4) . ":**");
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Recheck($inWb, $inTable) {
        if (empty($inWb) or empty($inTable)) return base\BaseXhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Missing inputs.");
        if (!mod\ModWbTable::CommandEnabledCheck($inWb, $inTable, __FUNCTION__)) return base\BaseXhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Method '".__FUNCTION__."' not enabled.");
        // Cleanup
        if (!base\Base::ClassExists('datatable\Datatable')) return base\BaseXhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Class Datatable not installed.");
        datatable\Datatable::Recheck($inWb,$inTable);
    }
    /* -------------------------------------------------------------------- */
    public static function Clear($inWb, $inTable) {
        if (empty($inWb) or empty($inTable)) return base\BaseXhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Missing inputs.");
        if (!mod\ModWbTable::CommandEnabledCheck($inWb, $inTable, __FUNCTION__)) return base\BaseXhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Method '" . __FUNCTION__ . "' not enabled.");
        // Clear
        base\BaseXhtmlMsg::Echo('Info', __METHOD__, "$inWb $inTable", "");
        base\BaseXhtmlMsg::Echo('Notice', __METHOD__, "$inWb $inTable", 'TODO - not yet implemented.');
    }
    /* -------------------------------------------------------------------- */
    public static function Reimport($inWb, $inTable) {
        if (empty($inWb) or empty($inTable)) return base\BaseXhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Missing inputs.");
        if (!mod\ModWbTable::CommandEnabledCheck($inWb, $inTable, __FUNCTION__)) return base\BaseXhtmlMsg::Echo('Warning', __METHOD__, "$inWb $inTable", "Method '" . __FUNCTION__ . "' not enabled.");
        base\BaseXhtmlMsg::Echo('Info', __METHOD__, "$inWb $inTable", "");
        base\BaseXhtmlMsg::Echo('Notice', __METHOD__, "$inWb $inTable", 'TODO - not yet implemented.');
    }
    /* -------------------------------------------------------------------- */
}