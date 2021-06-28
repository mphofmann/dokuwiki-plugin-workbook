<?php
namespace workbook\wbincdoku\doku;
use Doku_Renderer_xhtml;
use dokuwiki\plugin\struct\meta;
use syntax_plugin_struct_global;
use syntax_plugin_struct_list;
use syntax_plugin_struct_table;
use syntax_plugin_structgantt;
use Throwable;
class DokuDataStruct {
    /* -------------------------------------------------------------------- */
    private static $__RowLimit = '50';
    /* -------------------------------------------------------------------- */
    public static function MetaSchemaJsonExport($inSchema): string {
        $builder = new meta\Schema($inSchema);
        return $builder->toJSON();
    }
    /* -------------------------------------------------------------------- */
    public static function MetaSchemaJsonImport($inSchema, $inJson): bool {
        $return = false;
        $builder = new meta\SchemaImporter($inSchema, $inJson);
        if ($builder->build()) {
            touch(DokuConf::ConfGet('cachedir') . '/struct.schemarefresh'); // doesnt work: touch(action_plugin_struct_cache::getSchemaRefreshFile());
            $return = true;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function QueryArs(array $inSchemas = [], array $inCols = [], array $inFilters = [], array $inSorts = [], string $inLimit = '', bool $setIds = true): array {
        return self::__QueryAggregationArs(self::__ParaAr(['schema' => $inSchemas, 'cols' => $inCols, 'filters' => $inFilters, 'sorts' => $inSorts, 'limit' => $inLimit]), $setIds);
    }
    /* -------------------------------------------------------------------- */
    // @param array  $schemas array of strings with the schema-names
    // @param array  $cols array of strings with the columns
    // @param array  $filter array of arrays with ['logic'=> 'and'|'or', 'condition' => 'your condition']
    // @param string $sort string indicating the column to sort by
    public static function RowAr($inTable, $inId): array {
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
        } catch (Throwable $t) {
            DokuAreaMsg::ThrowableAdd('Warning', $t);
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    public static function RowDelete($inTable, $inId): bool {
        $return = true;
        try {
            if (strpos($inTable, '_page')) {
                $schema = new meta\Schema($inTable);
                $obj = new meta\AccessTablePage($schema, $inId, time());
                $return = $obj->clearData();
            } else {
                $schema = new meta\Schema($inTable);
                $obj = new meta\AccessTableGlobal($schema, $inId, time());
                $return = $obj->clearData();
            }
            if ($return === false) {
                DokuAreaMsg::Add('Notice', __METHOD__, "$inTable $inId", "Delete failed.");
            }
        } catch (Throwable $t) {
            DokuAreaMsg::ThrowableAdd('Warning', $t);
            $return = false;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function RowSave($inTable, $inId = '', $inChanges = []): bool { // update or insert[$inId=0]
        if (empty($inChanges)) {
            DokuAreaMsg::Add('Notice', __METHOD__, "$inTable $inId", "Input array is empty. Saving failed.");
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
                DokuAreaMsg::Add('Notice', __METHOD__, "$inTable $inId", "Saving failed: " . print_r($inChanges, true));
                return false;
            }
        } catch (Throwable $t) {
            DokuAreaMsg::ThrowableAdd('Warning', $t);
            return false;
        }
        return true;
    }
    /* -------------------------------------------------------------------- */
    public static function TableXhtmlGet($inWb, $inSheet, $inParas = [], $inType = 'table'): string {
        if (DokuPlugin::DisabledIs('struct')) return false;
        $type = ($inType == 'gantt') ? 'structgantt' : "struct_{$inType}";
        $p = DokuPlugin::LoadObj('syntax', $type);
        if ( ! $p) return false;
        $return = '';
        // paras
        $defaults = ($inType == 'list') ? ['cols' => '*',] : ['cols' => '*', 'limit' => self::$__RowLimit, 'dynfilters' => '1', 'summarize' => '0', 'csv' => '0'];
        $paras = array_replace($defaults, $inParas);
        if (empty($paras['schemas'])) $paras['schemas'] = ["{$inWb}_{$inSheet}"];
        $paras = self::__ParaAr($paras);
        // render
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
                DokuAreaMsg::Add('Warning', __METHOD__, "$inWb, $inSheet, $inType", "Type'$inType' unknown.");
                break;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    private static function __ParaAr($inAr): array {
        $returns = $inAr;
        $ar = [];
        foreach ($returns['schemas'] as $val) {
            $ar[] = [$val, ''];
        }
        $returns['schemas'] = $ar;
        if (isset($returns['sorts']) and ! isset($returns['sort'])) {
            $returns['sort'] = $returns['sorts'];
            unset($returns['sorts']);
        }
        if (isset($returns['filters']) and ! isset($returns['filter'])) {
            foreach ($returns['filters'] as $ar) {
                if (strpos('OR AND', end($ar)) === false) $ar[] = 'AND';
                $returns['filter'][] = $ar;
            }
            unset($returns['filters']);
        }
        // \_Wb_::DebugEcho($inAr, 'PARAAR-IN');
        // \_Wb_::DebugEcho($returns, 'PARAAR-OUT');
        // return self::__ZOLDQueryParaAr($inAr['schemas'] ?? [], $inAr['cols'] ?? [], $inAr['filters'] ?? [], $inAr['sorts'] ?? [], $inAr['limit']);
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    private static function __QueryAggregationArs($inParas = [], $setIds = true): array {
        // $inType
        // - query: regular query
        // - setIds: query + set id to rid/pid
        $returns = [];
        try {
            $search = new meta\SearchConfig($inParas);
            $results = $search->execute();
            if ($setIds) {
                $ids = strpos($inParas['schemas'][0][0], '_page') === false ? $search->getRids() : $search->getPids();
            }
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
                $returns[$id] = $add;
            }
        } catch (Throwable $t) {
            DokuAreaMsg::ThrowableAdd('Warning', $t);
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    private static function __ZOLDQueryParaAr(array $inSchemas = [], array $inCols = [], array $inFilters = [], array $inSorts = [], $inLimit = ''): array {
        $returns = [];
        $strschema = 'schema: ' . implode(', ', $inSchemas);
        $strcol = 'cols: ' . implode(', ', $inCols);
        $strsort = 'sort: ' . implode(', ', $inSorts);
        $filters = array_map(function ($ar) {
            $logic = '';
            $condition = '';
            if (isset($ar['logic']) and isset($ar['condition'])) {
                $logic = strtolower($ar['logic']);
                $condition = $ar['condition'];
            } else {
                foreach ($ar as $val) {
                    if (strpos('OR AND', $val) !== false) $logic = strtolower($val); else $condition .= " $val";
                }
            }
            return "filter$logic: $condition";
        }, $inFilters);
        $strlimit = "limit: $inLimit";
        \_Wb_::DebugEcho([$strschema, $strcol, $strsort, $strlimit]);
        \_Wb_::DebugEcho($filters);
        try {
            $parser = new meta\ConfigParser(array_merge([$strschema, $strcol, $strsort, $strlimit], $filters));
            $returns = $parser->getConfig();
        } catch (Throwable $t) {
            DokuAreaMsg::ThrowableAdd('Warning', $t);
        }
        \_Wb_::DebugEcho($returns);
        return $returns;
    }
    /* -------------------------------------------------------------------- */
}