<?php
namespace workbook\wbinc\admin;
class AdminXhtml {
    /* -------------------------------------------------------------------- */
    public static function MenuGet($doHr = false) {
        $return = "<div style='margin-bottom:5px'>";
        $ar = [];
        if (@$_SERVER['REMOTE_USER'] == 'admin') {
            $ar[] = "<a href='?do=admin&page=workbook_install'>01 Install</a>";
            $ar[] = "<a href='?do=admin&page=workbook_conf'>02 Conf</a>";
        }
        $ar[] = "<a href='?do=admin&page=workbook_operating'>1 Operating</a>";
        $ar[] = "<a href='?do=admin&page=workbook_marketplace'>2 Marketplace</a>";
        $ar[] = "<a href='?do=admin&page=workbook_workbook'>3 Workbook</a>";
        $ar[] = "<a href='?do=admin&page=workbook_content'>4 Content</a>";
        $return .= implode(' &raquo; ', $ar);
        $return .= "</div>";
        if ($doHr) $return .= "<hr style='margin-top:0.5em'>";
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function TextInstallLinkGet() {
        return '<h2>The Workbooks are not installed yet. Do the <a href="?do=admin&page=workbook_install">installation</a> first.</h2>';
    }
    /* -------------------------------------------------------------------- */
    public static function FormGet($inXhtml, $inAdminPlugin = '', $inAdminTab = '') {
        $return = "<form action='' method='post'>";
        $return .= formSecurityToken(false);
        $return .= $inXhtml;
        if (!empty($inAdminPlugin)) {
            $return .= '<input type="hidden" name="do" value="admin"/>';
            $str = $inAdminPlugin;
            if (!empty($inAdminTab)) $str .= "_$inAdminTab";
            $return .= "<input type='hidden' name='page' value='{$str}'/>";
        }
        $return .= '</form>';
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function ButtonGet($inCmd, $inValue = '', $inAttr = '', $inType = '', $inConfirm = '') {
        $value = (empty($inValue)) ? $inCmd : $inValue;
        $style = '';
        switch ($inType) {
            case 'small':
                $style .= 'padding:0.25em; font-size:0.8em;';
                break;
            case 'xsmall':
                $style .= 'min-width:0; padding:0.25em; font-size:0.6em;';
                break;
            case 'xxsmall':
                $style .= 'min-width:0; padding:0.25em; font-size:0.4em;';
                break;
            default:
                $style .= 'min-width:50px;';
                break;
        }
        $onclick = (empty($inConfirm)) ? '' : "onclick='if(confirm(&quot;{$inConfirm}&quot;)){}else{return false;}'";
        return "<button type='submit' name='cmd[{$inCmd}]' style='{$style}' {$inAttr} {$onclick}>{$value}</button>"; // return "<input type='submit' name='cmd[{$inCmd}]' value='{$value}' style='{$inStyle}' {$inAttr} />";
    }
    /* -------------------------------------------------------------------- */
    public static function SelectGet($inArray, $inSelected = '', $attrs = []) {
        $strattr = '';
        if (!empty($attrs)) {
            foreach ($attrs as $id => $val) $strattr .= "$id=\"$val\" ";
        }
        $return = "<select $strattr>";
        foreach ($inArray as $id => $val) {
            $selected = (!empty($val) and ($val == $inSelected)) ? 'selected' : '';
            $return .= "<option value='$id' $selected>$val</option>";
        }
        $return .= "</select>";
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function LinkGet($inUrl, $inText = '&raquo;&raquo;&raquo;') {
        return "<a href='$inUrl'>$inText</a>";
    }
    /* -------------------------------------------------------------------- */
    public static function StatusGet($inColor, $inTitle = '') {
        $ar = array('white' => '#ccc', 'green' => '#4c4', 'yellow' => '#cc4', 'orange' => '#c84', 'red' => '#c44',);
        $color = (isset($ar[$inColor])) ? $ar[$inColor] : $inColor;
        $strtitle = (empty($inTitle)) ? '' : "title='$inTitle'";
        return "<span style='color:$color; cursor:pointer;' $strtitle>&#x2b24;</span>";
    }
    /* -------------------------------------------------------------------- */
    public static function TableGet($inArray, $inStyles = []) {
        $return = '<div class="table">';
        $return .= '<table class="inline" style="width:100%">';
        foreach ($inArray as $row) {
            $return .= '<tr>';
            switch (count($row)) {
                case 1:
                case 0:
                    $val = (count($row) == 1) ? $row[0] : '&nbsp;';
                    $str = ' colspan="99" ';
                    $td = (substr($val, 0, 3) == 'TH:') ? 'th' : 'td';
                    $val = ($td == 'th') ? substr($val, 3) : $val;
                    $return .= "<$td $str><b>$val</b></$td>";
                    break;
                default:
                    foreach ($row as $id => $val) {
                        $td = (substr($val, 0, 3) == 'TH:') ? 'th' : 'td';
                        $val = ($td == 'th') ? substr($val, 3) : $val;
                        $style = @$inStyles[$id];
                        $return .= "<$td style='{$style}'>$val</$td>";
                    }
                    break;
            }
            $return .= '</tr>';
        }
        $return .= '</table>';
        $return .= '</div>';
        return $return;
    }
    /* -------------------------------------------------------------------- */
}