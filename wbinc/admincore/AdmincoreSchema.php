<?php
namespace workbook\wbinc\admincore;
use workbookcore\wbinc\base;
use workbookcore\wbinc\env;
use workbookcore\wbinc\sys;
use workbookcore\wbinc\xhtml;
/* -------------------------------------------------------------------- */
class AdmincoreSchema {
    /*
     * Types
     * - Mail     Mail
     * - Note     LongText
     * - Date     Date
     * - Datetime DateTime
     * - Page^^    Page
     * - ...^^    Datapage
     * - ...^     Datatable
     * - ...°     Dropdown
     * - Nr       Decimal
     * - Link     Url
     * - User     User
     * - ...      Text
     */
    /* -------------------------------------------------------------------- */
    private static $__Field2FieldClasses = [ //
        'IdMail' => 'Mail', //
        'IdPage' => 'Page', //
        'IdUrl' => 'Url', //
        'Link' => 'Url', //
        'Note' => 'Longtext', //
        'Nr' => 'Decimal', //
        'Rating' => 'Decimal', //
        'RowDateInsert' => 'Date', //
        'RowDateUpdate' => 'Date', //
        'StatusDate' => 'Date', //
        'StatusText' => 'Text', //
    ];
    /* -------------------------------------------------------------------- */
    private static $__FieldClasses = [ // struct types
        'Datatable' => ['class' => 'Lookup', 'config' => ['schema' => '$table$', 'field' => 'id',],], //
        'Datapage' => ['class' => 'Page', 'config' => ['usetitles' => true, 'autocomplete' => ['mininput' => 2, 'maxresult' => 5, 'namespace' => '$wb$:datapage:$datapage$', 'postfix' => ''],],], //
        'Date' => ['class' => 'Date', 'config' => ['format' => 'Y-m-d', 'prefilltoday' => false]], //
        'Decimal' => ['class' => 'Decimal', 'config' => ['min' => '', 'max' => '', 'roundto' => '-1', 'decpoint' => '.', 'thousands' => "'", 'trimzeros' => true, 'prefix' => '', 'postfix' => '',]], //
        'Dropdown' => ['class' => 'Dropdown', 'config' => ['values' => '$vr$'],], //
        'Longtext' => ['class' => 'LongText', 'config' => ['prefix' => '', 'postfix' => '', 'rows' => '5', 'cols' => '50',],], //
        'Mail' => ['class' => 'Mail', 'config' => ['prefix' => '', 'postfix' => ''],], //
        'Page' => ['class' => 'Page', 'config' => ['usetitles' => true, 'autocomplete' => ['mininput' => 2, 'maxresult' => 5, 'namespace' => '', 'postfix' => ''],],], //
        'Tag' => ['class' => 'Tag', 'config' => ['page' => '', 'autocomplete' => ['mininput' => 2, 'maxresult' => 5]]], //
        'Text' => ['class' => 'Text', 'config' => ['prefix' => '', 'postfix' => ''],], //
        'User' => ['class' => 'User', 'config' => ['existingonly' => true, 'autocomplete' => ['fullname' => true, 'mininput' => 2, 'maxresult' => 5,],],], //
        'Url' => ['class' => 'Url', 'config' => ['autoscheme' => 'http', 'prefix' => '', 'postfix' => '']], //
        // not used
        'Checkbox' => ['class' => 'Checkbox',], //
        'Color' => ['class' => 'Color',], //
        'Datetime' => ['class' => 'DateTime',], // does't really work
        'Media' => ['class' => 'Media',], //
        'Pagesummary' => ['class' => 'Summary',], //
        'Wiki' => ['class' => 'Wiki',], //
    ];
    private static $__FieldConfigs = [];
    /* -------------------------------------------------------------------- */
    public static function JsonGenerateGet($inSchema, $inFields) {
        $wb = self::__WbGet($inSchema);
        $table = self::__TableGet($inSchema);
        $returns = [];
        // $returns['structversion'] = '2018-09-11';
        $returns['schema'] = "{$wb}_{$table}";
        $returns['id'] = date('YmdHis');
        $returns['user'] = 'admin';
        $returns['config']['allowed editors'] = "@{$wb}_m,@{$wb}_l,@{$wb}_s";
        $returns['config']['label']['en'] = (substr($table, 0, 4) == 'page') ? strtoupper(substr($table, 4)) : strtoupper($table);
        $i = 1;
        $fields = (is_array($inFields)) ? $inFields : explode(',', $inFields);
        foreach ($fields as $field) {
            if (substr($field, 0, 1) == '%') continue; // %pageid% %lastupdate% %lastuser% %lastsummary%
            $returns['columns'][] = self::JsonColumnGet($field, $i);
            $i++;
        }
        $return = json_encode($returns);
        $return = strtr($return, ['$wb$' => $wb, '$table$' => $table, '$lang$' => env\EnvLang::Get('1'), '$currency$' => env\EnvConf::CurrencyGet()]);
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function JsonColumnGet($inField, $inSort) {
        $col = [];
        // Id
        $col['colref'] = $inSort;
        $col['label'] = $inField;
        // field
        $col['sort'] = $inSort;
        $col['ismulti'] = false;
        $col['isenabled'] = true;
        $col['config']['visibility']['inpage'] = true;
        $col['config']['visibility']['ineditor'] = true;
        $col['config']['label']['en'] = '';
        $col['config']['hint']['en'] = '';
        $field = $inField;
        // multi
        if (substr($field, -1) == '*') {
            $field = substr($field, 0, -1);
            $col['ismulti'] = true;
        }
        // values
        $arvalues = [];
        if (strpos($field, '[') !== false) { // Dropdown
            $pieces = explode('[', $field);
            $field = $pieces[0];
            $col['label'] = $field;
            $arvalues['config']['values'] = strtr($pieces[1], [']' => '', '|' => ',']);
        }
        $arclass = self::$__FieldClasses[self::__FieldClassGet($field)];
        $arclass['config']['autocomplete']['namespace'] = strtr($arclass['config']['autocomplete']['namespace'], ['$datapage$' => strtolower(strtr($field, ['^' => '']))]);
        $arconfs = self::__ConfigAr($field);
        $arlabel = self::__LabelHintAr($field);
        $return = array_replace_recursive($col, $arclass, $arconfs, $arvalues, $arlabel);
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function JsonLabelColrefRemapGet($inJsonOld, $inJsonNew) {
        $mapold = [];
        $old = json_decode($inJsonOld, true);
        if (is_array($old['columns'])) {
            foreach ($old['columns'] as $col) {
                $mapold[$col['label']] = $col['colref'];
            }
        }
        $i = count($mapold);
        $new = json_decode($inJsonNew, true);
        if (is_array($new['columns'])) {
            foreach ($new['columns'] as $id => $col) {
                if (isset($mapold[$col['label']])) { // existing column
                    $new['columns'][$id]['colref'] = $mapold[$col['label']];
                    unset($mapold[$col['label']]);
                } else { // new column
                    $i++;
                    $new['columns'][$id]['colref'] = $i;
                }
            }
        }
        if (is_array($old['columns'])) {
            foreach ($old['columns'] as $id => $col) {
                if (isset($mapold[$col['label']])) {  // remaining column
                    $col['sort'] = 100 + $i;
                    $col['isenabled'] = false;
                    $new['columns'][] = $col;
                    $i++;
                    unset($mapold[$col['label']]);
                }
            }
        }
        if (!empty($mapold)) {
            xhtml\XhtmlMsg::Echo('Error', __METHOD__, '', 'Json field mapping incorrect.');
            xhtml\XhtmlMsg::PrintR($mapold);
        }
        return json_encode($new);
    }
    /* -------------------------------------------------------------------- */
    public static function JsonExport($inSchema) {
        return base\BaseDataStruct::MetaSchemaJsonExport($inSchema);
    }
    /* -------------------------------------------------------------------- */
    public static function JsonImport($inSchema, $inJson) {
        $rc = base\BaseDataStruct::MetaSchemaJsonImport($inSchema, $inJson);
        if ($rc) {
            xhtml\XhtmlMsg::Echo('Info', __METHOD__, $inSchema, 'Schema refreshed.');
        } else {
            xhtml\XhtmlMsg::Echo('Error', __METHOD__, $inSchema, 'Something went wrong while saving.');
        }
    }
    /* -------------------------------------------------------------------- */
    public static function AssignmentPatternUpdate($inSchema, $inPattern) {
        $res = base\BaseDataSqlite::Query("DELETE FROM schema_assignments_patterns WHERE tbl='{$inSchema}'");
        $res = base\BaseDataSqlite::ArInsert('schema_assignments_patterns', ['pattern' => $inPattern, 'tbl' => $inSchema]);
        return $res;
    }
    /* -------------------------------------------------------------------- */
    private static function __WbGet($inSchema) {
        $pieces = explode('_', $inSchema);
        return $pieces[0];
    }
    /* -------------------------------------------------------------------- */
    private static function __TableGet($inSchema) {
        $pieces = explode('_', $inSchema);
        return $pieces[1];
    }
    /* -------------------------------------------------------------------- */
    private static function __FieldClassGet($inField) {
        $return = 'Text';
        if (strpos($inField, 'Page^^') !== false) {
            $return = 'Page';
        } elseif (strpos($inField, '^^') !== false) {
            $return = 'Datapage';
        } elseif (strpos($inField, '^') !== false) {
            $return = 'Datatable';
        } elseif (strpos($inField, '°') !== false) {
            $return = 'Dropdown';
        } else {
            $pieces = self::__CamelAr($inField);
            $first = $pieces[0];
            $last = array_pop($pieces);
            if (isset(self::$__Field2FieldClasses[$inField])) { // mapping full
                $return = self::$__Field2FieldClasses[$inField];
            } elseif (isset(self::$__Field2FieldClasses[$first])) { // mapping first camel
                $return = self::$__Field2FieldClasses[$first];
            } elseif (isset(self::$__Field2FieldClasses[$last])) { // mapping last camel
                $return = self::$__Field2FieldClasses[$last];
            } elseif (isset(self::$__FieldClasses[$first])) { // first
                $return = $first;
            } elseif (isset(self::$__FieldClasses[$last])) { // last
                $return = $last;
            }
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    private static function __ConfigAr($inField) {
        $returns = [];
        if (empty(self::$__FieldConfigs)) {
            self::$__FieldConfigs = sys\SysNsid::IniAr("zsync:sync:" . base\Base::VersionGet() . ":db:db_fieldconfigs.ini", '');
        }
        if (isset(self::$__FieldConfigs[$inField])) {
            $returns['config'] = self::$__FieldConfigs[$inField];
        } elseif (strpos($inField, '^') !== false) {
            $pieces = explode('^', $inField);
            $returns['config'] = ['schema' => '$wb$_' . strtolower($pieces[0])];
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    private static function __LabelHintAr($inField) {
        $returns = [];
        $returns['config']['label']['en'] = $inField;
        /* $label = $inField;
        $pieces = self::__CamelAr($label);
        if (count($pieces) > 1) {
            array_shift($pieces);
            $label = implode('', $pieces);
        }
        if (!empty($label)) {
            $label = strtr($label, ['^' => '', '°' => '']);
            $label .= (strpos($inField, '^') === false) ? '' : '^';  // e.g. Account^Debit => Debit^
            $returns['config']['label']['en'] = $label;
        } */
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    private static function __CamelAr($inField) {
        $pieces = preg_split('/(?=[A-Z])/', $inField);
        $piece = array_shift($pieces);
        return $pieces;
    }
    /* -------------------------------------------------------------------- */
}
