<?php
namespace workbook\wbdef\mainadmin;
use workbook\wbinc\baseadmin;
abstract class a_Mainadmin {
    /* -------------------------------------------------------------------- */
    protected static $_AdminOnly = false;
    protected static $_Cmds = ['cmd' => ''];
    protected static $_Icon = '';
    protected static $_Item = '';
    protected static $_Note = '';
    protected static $_Out = '';
    /* -------------------------------------------------------------------- */
    public static function AdminOnlyCheck(): bool {
        return static::$_AdminOnly;
    }
    /* -------------------------------------------------------------------- */
    public static function FieldGet($inField): string {
        $return = '';
        switch ($inField) {
            case 'icon':
                $return = static::$_Icon;
                break;
            case 'note':
                $return = static::$_Note;
                break;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function Get(): string {
        $return = '';
        if (static::$_AdminOnly and $_SERVER['REMOTE_USER'] !== 'admin') {
            $return .= baseadmin\BaseadminXhtmlMsg::Get('Info', '', '', 'No access');
        } else {
            self::HandleExec();
            $return .= self::XhtmlGet();
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function HandleExec(): void {
        $str = baseadmin\BaseadminExec::HandleGet(static::$_Cmds);
        if ($str === false) return;
        static::$_Out = $str;
    }
    /* -------------------------------------------------------------------- */
    public static function XhtmlGet(): string {
        $return = '';
        // Output
        $return .= baseadmin\BaseadminExec::OutputGet(static::$_Out);
        // Heading
        $return .= "<h2><i class='" . static::$_Icon . "'></i> " . ucfirst(static::$_Item) . '</h2>';
        // Xhtml
        $return .= static::_XhtmlGet();
        // Form
        $return .= '<form action="?" method="post">';
        $return .= '<input type="hidden" name="do"   value="admin" />';
        $return .= '<input type="hidden" name="page" value="workbook_admin" />';
        $return .= '<input type="hidden" name="wb_main" value="' . static::$_Item . '" />';
        // doku\DokuXhtml::SecTokenEcho();
        // Table
        $ar = array_merge(static::_Array01Get(), static::_Array02Get(), static::_Array03Get(), static::_Array04Get(), static::_Array05Get(), static::_Array06Get(), static::_Array07Get(), static::_Array08Get(), static::_Array09Get(), static::_Array10Get(), static::_Array11Get(), static::_Array12Get());
        if ( ! empty($ar)) {
            $return .= baseadmin\BaseadminXhtml::TableGet($ar, static::_StylesAr());
            $return .= '<br/>';
        }
        // Form
        $return .= '</form>';
        return $return;
    }
    /* -------------------------------------------------------------------- */
    protected static function _Array01Get(): array {
        return [];
    }
    /* -------------------------------------------------------------------- */
    protected static function _Array02Get(): array {
        return [];
    }
    /* -------------------------------------------------------------------- */
    protected static function _Array03Get(): array {
        return [];
    }
    /* -------------------------------------------------------------------- */
    protected static function _Array04Get(): array {
        return [];
    }
    /* -------------------------------------------------------------------- */
    protected static function _Array05Get(): array {
        return [];
    }
    /* -------------------------------------------------------------------- */
    protected static function _Array06Get(): array {
        return [];
    }
    /* -------------------------------------------------------------------- */
    protected static function _Array07Get(): array {
        return [];
    }
    /* -------------------------------------------------------------------- */
    protected static function _Array08Get(): array {
        return [];
    }
    /* -------------------------------------------------------------------- */
    protected static function _Array09Get(): array {
        return [];
    }
    /* -------------------------------------------------------------------- */
    protected static function _Array10Get(): array {
        return [];
    }
    /* -------------------------------------------------------------------- */
    protected static function _Array11Get(): array {
        return [];
    }
    /* -------------------------------------------------------------------- */
    protected static function _Array12Get(): array {
        return [];
    }
    /* -------------------------------------------------------------------- */
    protected static function _StylesAr(): array {
        return [ //
            'width:130px; height:35px; white-space:nowrap;', //
            '', //
            'width:130px; text-align:center;', //
            'width:130px; white-space:nowrap; text-align:center;', //
            'width:130px; white-space:nowrap; text-align:center;', //
        ];
    }
    /* -------------------------------------------------------------------- */
    protected static function _XhtmlGet(): string {
        return '';
    }
    /* -------------------------------------------------------------------- */
}