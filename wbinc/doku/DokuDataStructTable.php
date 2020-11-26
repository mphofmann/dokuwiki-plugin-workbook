<?php
namespace workbook\wbinc\doku;
use Doku_Renderer_xhtml;
use syntax_plugin_struct_list;
use syntax_plugin_struct_global;
use syntax_plugin_struct_table;
use syntax_plugin_structgantt;
class DokuDataStructTable {
    /* -------------------------------------------------------------------- */
    private static $__Confmetadir = '';
    private static $__RowLimit = '50';
    /* -------------------------------------------------------------------- */
    public static function XhtmlGet($inWb, $inTable, $inWiki = 'local', $inType = 'table', $inParas = []) {
        if (DokuXhtmlPlugin::DisabledIs('struct')) return false;
        $type = ($inType == 'gantt') ? 'structgantt' : "struct_{$inType}";
        $p = DokuXhtmlPlugin::Load('syntax', $type);
        if (!$p) return false;
        $return = '';
        // defaults
        switch ($inType) {
            case 'list':
                $defaults = ['cols' => '*',];
                break;
            default:
                $defaults = ['cols' => '*', 'limit' => self::$__RowLimit, 'dynfilters' => '1', 'summarize' => '0', 'csv' => '0'];
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
    /* -------------------------------------------------------------------- */
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
    /* -------------------------------------------------------------------- */
    private static function __WikiPathReset() {
        if (!empty(self::$__Confmetadir)) {
            global $conf;
            $conf['metadir'] = self::$__Confmetadir;
            self::$__Confmetadir;
        }
    }
    /* -------------------------------------------------------------------- */
}