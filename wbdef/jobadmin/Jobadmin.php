<?php
namespace workbook\wbdef\jobadmin;
use workbook\wbinc\admin;
class Jobadmin extends a_Jobadmin {
    /* -------------------------------------------------------------------- */
    protected static $_Item = '';
    protected static $_Icon = '';
    protected static $_Note = '';
    /* -------------------------------------------------------------------- */
    protected static function _XhtmlGet(): string {
        $return = "<table class='inline' style='width:100%'>";
        foreach (admin\AdminXhtml::MenuAr() as $id => $ar) {
            $return .= "<tr><th colspan='2'>" . ucfirst($id) . "</th></tr>";
            foreach ($ar as $id2 => $val2) {
                $icon_ = '';
                if(!empty($id2)){
                    $icon = \_Wb_::CmdExec("jobadmin\\Jobadmin{$id2}::FieldGet field=icon");
                    $icon_ = "<i class='$icon'></i>"." ";
                }
                $note = \_Wb_::CmdExec("jobadmin\\Jobadmin{$id2}::FieldGet field=note");
                $return .= "<tr><td>{$icon_}$val2</td><td>$note</td></tr>";
            }
        }
        $return .= "</table>";
        return $return;
    }
    /* -------------------------------------------------------------------- */
}