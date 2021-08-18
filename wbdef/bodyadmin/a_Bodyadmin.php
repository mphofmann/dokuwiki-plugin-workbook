<?php
namespace workbook\wbdef\bodyadmin;
use workbook\wbinc\baseadmin;
abstract class a_Bodyadmin {
    /* -------------------------------------------------------------------- */
    protected static $_Item = '';
    protected static $_Icon = '';
    protected static $_Note = '';
    protected static $_AdminOnly = false;
    protected static $_Out = '';
    protected static $_Cmds = ['cmd' => ''];
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
    public static function AdminOnlyCheck(): bool {
        return static::$_AdminOnly;
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
        $return .= '<input type="hidden" name="wb_item" value="' . static::$_Item . '" />';
        // doku\DokuXhtml::SecTokenEcho();
        // Table
        $ar = static::_Array1Get();
        if (!empty($ar)) {
            $return .= baseadmin\BaseadminXhtml::TableGet($ar, static::_StylesAr());
            $return .= '<br/>';
        }
        $ar = static::_Array2Get();
        if (!empty($ar)) {
            $return .= baseadmin\BaseadminXhtml::TableGet($ar, static::_StylesAr());
            $return .= '<br/>';
        }
        $ar = static::_Array3Get();
        if (!empty($ar)) {
            $return .= baseadmin\BaseadminXhtml::TableGet($ar, static::_StylesAr());
            $return .= '<br/>';
        }
        $ar = static::_Array4Get();
        if (!empty($ar)) {
            $return .= baseadmin\BaseadminXhtml::TableGet($ar, static::_StylesAr());
            $return .= '<br/>';
        }
        // Form
        $return .= '</form>';
        return $return;
    }
    /* -------------------------------------------------------------------- */
    protected static function _XhtmlGet(): string {
        return '';
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
    protected static function _Array1Get(): array {
        return [];
    }
    /* -------------------------------------------------------------------- */
    protected static function _Array2Get(): array {
        return [];
    }
    /* -------------------------------------------------------------------- */
    protected static function _Array3Get(): array {
        return [];
    }
    /* -------------------------------------------------------------------- */
    protected static function _Array4Get(): array {
        return [];
    }
    /* -------------------------------------------------------------------- */
}