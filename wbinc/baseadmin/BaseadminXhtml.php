<?php
namespace workbook\wbinc\baseadmin;
class BaseadminXhtml {
    /* -------------------------------------------------------------------- */
    private static $__Ar = [ //
        'admin' => ['Infra' => '', 'Module' => '', 'Dokuwiki' => '', 'Conf' => '', 'Config' => '',], //
        'workbook' => ['Operating' => '', 'Marketplace' => '', 'Workbook' => '', 'Content' => '',], //
        'module' => ['Cron' => '', 'Inbox' => '', 'Mailing' => '', 'Monitor' => '', 'User' => '',], //
        'addon' => ['Move' => '', 'Sqlite' => '', 'Structassignment' => '', 'Structeditor' => '',], //
        'other' => [], //
        'hidden' => [], //
    ];
    /* -------------------------------------------------------------------- */
    public static function A_Construct(): bool {
        if ( ! \_Wb_::RunarchCheck('doku')) {
            self::$__Ar['hidden'] = self::$__Ar['addon'];
            unset(self::$__Ar['addon']);
        }
        return true;
    }
    /* -------------------------------------------------------------------- */
    public static function ButtonGet($inCmd, $inValue = '', $inAttr = '', $inType = '', $inConfirm = ''): string {
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
        $_onclick = (empty($inConfirm)) ? '' : " onclick='if(confirm(&quot;{$inConfirm}&quot;)){}else{return false;}'";
        $str = trim($value, '[]');
        $_strtitle = ctype_alpha($str) ? " title='$str'" : '';
        return "<button type='submit' name='cmd[{$inCmd}]' style='{$style}' {$inAttr}{$_onclick}{$_strtitle}>{$value}</button>"; // return "<input type='submit' name='cmd[{$inCmd}]' value='{$value}' style='{$inStyle}' {$inAttr} />";
    }
    /* -------------------------------------------------------------------- */
    public static function DokuIframeGet($inTitle, $inQuerystring): string {
        $return = '';
        if (file_exists('doku.php')) {
            $return .= "<table class='inline iframe' style='width:100%'>";
            $return .= "<tr><th style='text-align:center;cursor:pointer;' onclick='window.open(\"doku.php?{$inQuerystring}\") ;'><i class='fas fa-external-link-alt'></i> $inTitle</th></tr>";
            $return .= "<tr><td><iframe src='dokuiframe.php?$inQuerystring' style='width:100%; height:1200px; resize:vertical;'></iframe></td></tr>";
            $return .= "</table>";
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function FormGet($inXhtml, $inAdminModule = '', $inAdminTab = ''): string {
        $return = "<form action='' method='post'>";
        // $return .= formSecurityToken(false); // TODO ?
        $return .= $inXhtml;
        if ( ! empty($inAdminModule)) {
            $return .= '<input type="hidden" name="do" value="admin"/>';
            $str = $inAdminModule;
            if ( ! empty($inAdminTab)) $str .= "_$inAdminTab";
            $return .= "<input type='hidden' name='page' value='{$str}'/>";
        }
        $return .= '</form>';
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function LinkGet($inUrl): string {
        if (substr($inUrl, 0, 6) == 'wb.php' and ! \_Wb_::RunarchCheck('wb')) return '';
        if (substr($inUrl, 0, 8) == 'doku.php' and ! \_Wb_::RunarchCheck('doku')) return '';
        if (substr($inUrl, 0, 6) == 'wb.php') {
            $text = '&raquo;&raquo;&raquo;';
        } elseif (substr($inUrl, 0, 8) == 'doku.php') {
            $text = '&gt;&gt;&gt;';
        } else {
            $text = (WB_CONTROLLER == 'doku.php') ? '&gt;&gt;&gt;' : '&raquo;&raquo;&raquo;';
        }
        return "<a href='$inUrl'>$text</a>";
    }
    /* -------------------------------------------------------------------- */
    public static function MenuAr(): array {
        $returns = self::$__Ar;
        $path = substr(WB_CONTROLLER, 0, 4) == 'doku' ? 'lib/plugins/' : 'workbook/module/';
        if (is_dir($path)) {
            foreach (scandir($path) as $val) {
                if (substr($val, 0, 1) == '.') continue;
                $dir = "{$path}{$val}/wbdef/mainadmin/";
                if ( ! is_dir($dir)) continue;
                foreach (scandir($dir) as $val2) {
                    if (substr($val2, 0, 1) == '.') continue;
                    if (substr($val2, 0, 2) == 'a_') continue;
                    $item = strtr($val2, ['Mainadmin' => '', '.php' => '']);
                    if (empty($item)) continue;
                    if (\_Wb_::CmdExec("mainadmin\\Mainadmin{$item}::AdminOnlyCheck") == true and $_SERVER['REMOTE_USER'] != 'admin') continue;
                    if (isset($returns['admin'][$item])) {
                        $returns['admin'][$item] = 'exists';
                    } elseif (isset($returns['workbook'][$item])) {
                        $returns['workbook'][$item] = 'exists';
                    } elseif (isset($returns['module'][$item])) {
                        $returns['module'][$item] = 'exists';
                    } elseif (isset($returns['addon'][$item])) {
                        $returns['addon'][$item] = 'exists';
                    } elseif (isset($returns['hidden'][$item])) {
                        continue;
                    } else {
                        $returns['other'][$item] = 'exists';
                    }
                }
            }
        }
        foreach ($returns as $id1 => $ar1) {
            foreach ($ar1 as $id2 => $val2) {
                if ($val2 == 'exists') {
                    $returns[$id1][$id2] = "<a href='?wb_job=admin&do=admin&page=workbook_admin&wb_main=" . strtolower($id2) . "'>$id2</a>";
                } else {
                    unset($returns[$id1][$id2]);
                }
            }
        }
        foreach ($returns as $id => $ar) {
            if (empty($returns[$id])) unset($returns[$id]);
        }
        if (empty($returns['admin'])) if (@$_SERVER['REMOTE_USER'] != 'admin') unset($returns['admin']);
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    public static function MenuGet($doHr = false): string {
        $return = "<div style='margin-bottom:5px'>";
        // strleft
        $strleft = "<a href='?wb_job=admin'><i class='fas fa-home'></i></a>" . " &nbsp; | &nbsp; ";
        // strcenter
        $strcenter = '';
        $ar = self::MenuAr();
        $strcenteraddon = '';
        if ( ! empty($ar['addon'])) {
            $strcenteraddon = '<br/>' . implode("&nbsp; | &nbsp; ", $ar['addon']);
            unset($ar['addon']);
        }
        end($ar);
        $idlast = key($ar);
        foreach ($ar as $id1 => $ar1) {
            $char = $id1 == 'other' ? '&nbsp;' : '&raquo;';
            $strcenter .= implode(" $char ", $ar1);
            if ($id1 != $idlast) $strcenter .= "&nbsp; | &nbsp; ";
        }
        // strright
        $ar = [];
        if (\_Wb_::RunarchCheck('wb')) {
            $ar[] = WB_CONTROLLER == 'wb.php' ? "<i class='fas fa-square' title='Workbook mode'></i>" : "<a href='wb.php?wb_job=admin&page=workbook_admin&wb_message=wbmode' title='Switch to Workbook mode'><i class='fas fa-square'></i></a>";
        }
        if (\_Wb_::RunarchCheck('doku')) {
            $ar[] = WB_CONTROLLER == 'doku.php' ? "<i class='fas fa-pen-square' title='Dokuwiki mode'></i>" : "<a href='doku.php?do=admin&page=workbook_admin&wb_message=dokumode' title='Switch to Dokuwiki mode'><i class='fas fa-pen-square'></i></a>";
        }
        $strright = " &nbsp; | &nbsp; " . implode(' ', $ar);
        // return
        $return .= "<table style='width:100%;'><tr><td style='min-width:40px;border:0;white-space:nowrap;'>$strleft</td><td style='border:0;text-align:center'>{$strcenter}{$strcenteraddon}</td><td style='min-width:40px;border:0;white-space:nowrap;text-align:right'>$strright</td></tr></table>";
        $return .= "</div>";
        if ($doHr) $return .= "<hr style='margin-top:0.5em'>";
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function SelectGet($inArray, $inSelected = '', $attrs = []): string {
        $strattr = '';
        if ( ! empty($attrs)) {
            foreach ($attrs as $id => $val) $strattr .= "$id=\"$val\" ";
        }
        $return = "<select $strattr>";
        foreach ($inArray as $id => $val) {
            $selected = ( ! empty($val) and ($val == $inSelected)) ? 'selected' : '';
            $return .= "<option value='$id' $selected>$val</option>";
        }
        $return .= "</select>";
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function StatusGet($inColor, $inTitle = ''): string {
        $ar = array('white' => '#fff', 'green' => '#4c4', 'yellow' => '#cc4', 'orange' => '#c84', 'red' => '#c44',);
        $color = (isset($ar[$inColor])) ? $ar[$inColor] : $inColor;
        $strtitle = (empty($inTitle)) ? '' : "title='$inTitle'";
        return "<span style='color:$color; cursor:pointer;' $strtitle>&#x2b24;</span>";
    }
    /* -------------------------------------------------------------------- */
    public static function TableGet($inArray, $inStyles = []): string {
        $return = '<div class="table">';
        $return .= '<table class="inline" style="width:100%">';
        foreach ($inArray as $row) {
            $return .= "<tr>";
            switch (count($row)) {
                case 1:
                case 0:
                    $val = (count($row) == 1) ? $row[0] : '&nbsp;';
                    $strattr = ' colspan="99" style="background:#eee"';
                    $td = (substr($val, 0, 3) == 'TH:') ? 'th' : 'td';
                    $val = ($td == 'th') ? substr($val, 3) : $val;
                    $return .= "<$td $strattr><b>$val</b></$td>";
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