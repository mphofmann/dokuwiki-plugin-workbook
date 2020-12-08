<?php
namespace workbook\wbinc\dokuadmin;
use workbook\wbinc\admin;
use workbook\wbinc\doku;
class DokuadminInfra {
    /* -------------------------------------------------------------------- */
    public static $Infras = [ //
        'PHP' => '', //
        'PHP-Ioncube' => '', //
        'Webroot' => '', //
        'Dokuwiki' => '', //
    ];
    /* -------------------------------------------------------------------- */
    public static function Action($inAction, $inId) {
        $return = '';
        switch ($inAction) {
            case 'note':
                $return .= self::$Infras[$inId];
                break;
            case 'status':
                $ar = ['PHP' => 'php-ok', 'PHP-Ioncube' => 'ioncube-ok', 'Dokuwiki' => 'dokuwiki-ok', 'Webroot' => 'webroot-ok'];
                $color = strpos(WB_RUNMODE, $ar[$inId]) === false ? 'red' : 'green';
                $return .= admin\AdminXhtml::StatusGet($color);
                break;
            case 'info':
                switch ($inId) {
                    case 'PHP':
                        phpinfo();
                        exit;
                        break;
                    case 'PHP-Ioncube':
                        $return .= extension_loaded('ioncube Loader') ? 'PHP-Ioncube extension loaded' : 'PHP-Ioncube extension missing';
                        break;
                    case 'Webroot':
                        $return .= strpos(WB_RUNMODE, 'webroot-ok') === false ? 'Webroot (' . getcwd() . ') is not writeable.' : 'Webroot (' . getcwd() . ') is writeable.';
                        break;
                    case 'Dokuwiki':
                        $return .= "See <a href='http://www.dokuwiki.org' target='_blank'>Dokuwiki</a>";
                        break;
                }
                break;
            case 'version':
                switch ($inId) {
                    case 'PHP':
                        $return .= PHP_VERSION;
                        break;
                    case 'Dokuwiki':
                        $return .= doku\DokuGlobal::VersionGet();
                        break;
                }
                break;
            default:
                admin\AdminXhtmlMsg::Echo('Warning', '', '', "Infra action unknown: $inAction $inId");
                break;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
}