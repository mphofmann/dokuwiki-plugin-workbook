<?php
namespace workbook\wbinc\doku;
use Doku_Renderer_xhtml;
use syntax_plugin_struct_list;
use syntax_plugin_struct_global;
use syntax_plugin_struct_table;
use syntax_plugin_structgantt;
use Throwable;
use dokuwiki\plugin\struct\meta;
class DokuDataStruct {
    /* -------------------------------------------------------------------- */
    private static $__Confmetadir = '';
    /* -------------------------------------------------------------------- */
    public static function MetaSchemaAllGet() {
        return meta\Schema::getAll();
    }
    /* -------------------------------------------------------------------- */
    public static function MetaSchemaJsonExport($inSchema) {
        $builder = new meta\Schema($inSchema);
        return $builder->toJSON();
    }
    /* -------------------------------------------------------------------- */
    public static function MetaSchemaJsonImport($inSchema, $inJson) {
        $return = false;
        $builder = new meta\SchemaImporter($inSchema, $inJson);
        if ($builder->build()) {
            touch(DokuSysGlobal::ConfGet('cachedir') . '/struct.schemarefresh'); // doesnt work: touch(action_plugin_struct_cache::getSchemaRefreshFile());
            $return = true;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function MetaCsvExport($inSchema) {
        DokuXhtmlMsg::Echo('Notice', __METHOD__, "$inSchema", 'TODO - not yet implemented.');
        $schema = meta\Schema::cleanTableName($inSchema);
        $type = (strpos($inSchema, '_page') === false) ? 'page' : 'global';
        ob_start();
        new meta\CSVExporter($schema, $type);
        ob_get_clean();
    }
    /* -------------------------------------------------------------------- */
    public static function MetaCsvImport($inSchema, $inCsv) {
        DokuXhtmlMsg::Echo('Notice', __METHOD__, "$inSchema", 'TODO - not yet implemented.');
    }
    /* -------------------------------------------------------------------- */
    // @param array  $schemas array of strings with the schema-names
    // @param array  $cols array of strings with the columns
    // @param array  $filter array of arrays with ['logic'=> 'and'|'or', 'condition' => 'your condition']
    // @param string $sort string indicating the column to sort by
    public static function ParasAr($inSchemas = [], $inCols = [], $inFilters = [], $inSorts = [], $inLimit = '') {
        $returns = [];
        $strschema = 'schema: ' . implode(', ', $inSchemas);
        $strcol = 'cols: ' . implode(', ', $inCols);
        $strsort = 'sort: ' . implode(', ', $inSorts);
        $filters = array_map(function ($filter) {
            return 'filter' . $filter['logic'] . ': ' . $filter['condition'];
        }, $inFilters);
        $strlimit = "limit: $inLimit";
        try {
            $parser = new meta\ConfigParser(array_merge([$strschema, $strcol, $strsort, $strlimit], $filters));
            $returns = $parser->getConfig();
        } catch (Throwable $e) {
            DokuXhtmlMsg::Add('Warning', '', '', $e->getMessage());
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    public static function AggregationAr($inParas = [], $setIds = true) {
        // $inType
        // - query: regular query
        // - setIds: query + set id to rid/pid
        try {
            $search = new meta\SearchConfig($inParas);
            $results = $search->execute();
            if ($setIds) {
                $ids = strpos($inParas['schemas'][0][0], '_page') === false ? $pids = $search->getRids() : $search->getPids();
            }
            $data = [];
            foreach ($results as $id => $row) {
                $add = [];
                foreach ($row as $value) {
                    $str = strpos($value->getColumn()->getLabel(), '^^') === false ? $value->getDisplayValue() : $value->getRawValue();
                    $add[$value->getColumn()->getFullQualifiedLabel()] = $str;
                }
                if ($setIds) {
                    $rpid = $ids[$id];
                    $id = (empty($rpid)) ? $id : $rpid;
                }
                $data[$id] = $add;
            }
            return $data;
        } catch (Throwable $e) {
            DokuXhtmlMsg::Add('Warning', '', '', $e->getMessage());
        }
    }
    /* -------------------------------------------------------------------- */
    public static function RowAr($inTable, $inId) {
        $returns = [];
        try {
            if (strpos($inTable, '_page') === false) { // global
                $schema = new meta\Schema($inTable, time());
                $obj = new meta\AccessTableGlobal($schema, '', 0, $inId);
            } else { // page
                $schema = new meta\Schema($inTable, 0);
                $obj = new meta\AccessTablePage($schema, $inId, 0, 0);
            }
            $returns = $obj->getDataArray();
        } catch (Throwable $e) {
            DokuXhtmlMsg::Add('Warning', '', '', $e->getMessage());
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    public static function RowSave($inTable, $inId = '', $inChanges = []) { // update or insert[$inId=0]
        if (empty($inChanges)) {
            DokuXhtmlMsg::Add('Notice', __METHOD__, "$inTable $inId", "Input array is empty. Saving failed.");
            return false;
        }
        try {
            $changes = [];
            foreach ($inChanges as $id => $val) { // remove fully-qualified-label
                $id2 = (substr($id, 0, strlen($inTable . '.')) == $inTable . '.') ? substr($id, strlen($inTable . '.')) : $id;
                $changes[$id2] = $val;
            }
            $rc = true;
            if (empty($inId)) {
                $ar = $changes;
            } else {
                $ar = self::RowAr($inTable, $inId);
                $ar = array_merge($ar, $changes);
            }
            if (strpos($inTable, '_page')) {
                $schema = new meta\Schema($inTable);
                $obj = new meta\AccessTablePage($schema, $inId, time());
                $rc = $obj->saveData($ar);
            } else {
                $schema = new meta\Schema($inTable);
                $obj = new meta\AccessTableGlobal($schema, '', time(), $inId);
                $rc = $obj->saveData($ar);
            }
            if ($rc === false) {
                DokuXhtmlMsg::Add('Notice', __METHOD__, "$inTable $inId", "Saving failed: " . print_r($inChanges, true));
                return false;
            }
        } catch (Throwable $e) {
            DokuXhtmlMsg::Add('Warning', '', '', $e->getMessage());
            return false;
        }
        return true;
    }
    /* -------------------------------------------------------------------- */
    public static function RowDelete($inTable, $inId) {
        $return = '';
        try {
            $rc = true;
            if (strpos($inTable, '_page')) {
                $schema = new meta\Schema($inTable);
                $obj = new meta\AccessTablePage($schema, $inId, time());
                $rc = $obj->clearData();
            } else {
                $schema = new meta\Schema($inTable);
                $obj = new meta\AccessTableGlobal($schema, $inId, time());
                $rc = $obj->clearData();
            }
            if ($rc === false) {
                DokuXhtmlMsg::Add('Notice', __METHOD__, "$inTable $inId", "Delete failed.");
            }
        } catch (Throwable $e) {
            DokuXhtmlMsg::Add('Warning', '', '', $e->getMessage());
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function TableXhtmlGet($inWb, $inTable, $inWiki = 'local', $inType = 'table', $inParas = []) {
        if (DokuXhtmlPlugin::DisabledIs('struct')) return false;
        $p = DokuXhtmlPlugin::Load('syntax', ($inType == 'gantt') ? 'structgantt' : "struct_{$inType}");
        if (!$p) return false;
        $return = '';
        // defaults
        switch ($inType) {
            case 'list':
                $defaults = ['cols' => '*',];
                break;
            default:
                $defaults = ['cols' => '*', 'limit' => '20', 'dynfilters' => '1', 'summarize' => '0', 'csv' => '0'];
                break;
        }
        $paras = array_replace($defaults, $inParas);
        if (empty($paras['schemas'])) $paras['schemas'][] = ["{$inWb}_{$inTable}"];
        // wiki & render
        if (self::__WikiPathSet($inWiki)) {
            $inMode = 'xhtml';
            $r = new Doku_Renderer_xhtml();
            switch ($inType) {
                case 'table':
                    $t = new syntax_plugin_struct_table();
                    $t->render($inMode, $r, $paras);
                    $return .= $r->doc;
                    break;
                case 'global':
                    $t = new syntax_plugin_struct_global();
                    $t->render($inMode, $r, $paras);
                    $return .= $r->doc;
                    break;
                case 'list':
                    $t = new syntax_plugin_struct_list();
                    $t->render($inMode, $r, $paras);
                    $return .= $r->doc;
                    break;
                case 'gantt':
                    $t = new syntax_plugin_structgantt();
                    $t->render($inMode, $r, $paras);
                    $return .= $r->doc;
                    break;
                default:
                    DokuXhtmlMsg::Add('Warning', __METHOD__, "$inWb, $inTable, $inWiki, $inType", "Type'$inType' unknown.");
                    break;
            }
        }
        self::__WikiPathReset();
        return $return;
    }
    private static function __WikiPathSet($inWiki) {
        $return = false;
        switch ($inWiki) {
            case 'local':
                $return = true;
                break;
            default:
                DokuXhtmlMsg::Add('Warning', __METHOD__, "$inWiki", "Wiki '$inWiki' unknown.");
                $return = false;
                break;
        }
        return $return;
    }
    private static function __WikiPathReset() {
        if (!empty(self::$__Confmetadir)) {
            global $conf;
            $conf['metadir'] = self::$__Confmetadir;
            self::$__Confmetadir;
        }
    }
    /* -------------------------------------------------------------------- */
}