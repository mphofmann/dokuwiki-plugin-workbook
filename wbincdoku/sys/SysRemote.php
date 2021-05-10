<?php
namespace workbook\wbincdoku\sys;
use IXR_Client;
use workbook\wbinc\admin;
use workbook\wbincdoku\doku;
class SysRemote {
    /* -------------------------------------------------------------------- */
    private static $__Client = '';
    private static $__ClientLoginCheck = false;
    private static $__Call = '';
    private static $__Status = '';
    private static $__Response = '';
    private static $__ErrorString = 'WB-EXCEPTION: ';
    private static $__ErrorMessage = '';
    /* -------------------------------------------------------------------- */
    public static function Exec($inAction) {
        $status = (self::EnabledCheck()) ? 'green' : 'red';
        switch ($inAction) {
            case 'status':
                echo admin\AdminXhtml::StatusGet($status);
                break;
            case 'login':
                if (self::EnabledCheck('login')) {
                    admin\AdminXhtmlMsg::Echo('Success', __METHOD__, '', 'Remote login successful.');
                } else {
                    admin\AdminXhtmlMsg::Echo('Warning', __METHOD__, '', 'Remote login failed.');
                }
                break;
            default:
                admin\AdminXhtmlMsg::Echo('Warning', __METHOD__, $inAction, 'Parameter unknown.');
                break;
        }
    }
    /* -------------------------------------------------------------------- */
    public static function EnabledCheck($inType = ''): bool {
        if (self::$__ClientLoginCheck) return true;
        $url = doku\DokuConf::ConfGet('plugin', 'workbook', 'connect_url');
        if (empty($url) or !filter_var($url, FILTER_VALIDATE_URL)) return false;
        $ar = ['username', 'password', 'mail', 'terms'];
        foreach ($ar as $val) {
            $str = doku\DokuConf::ConfGet('plugin', 'workbook', "connect_$val");
            if (empty($str) or $str == '!!not set!!') return false;
        }
        $return = true;
        if ($inType == 'login') {
            $return = self::__ClientLoginCheck();
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function SystemsAr(): array {
        $returns = [];
        try {
            if (!self::EnabledCheck('login')) return [];
            $cmd = "plugin.workbookhub.SysDataIdContentsGet";
            $version = doku\DokuConf::ConfGet('plugin', 'workbook', 'connect_version');
            $nsid = "zsync:sync:$version:conf:systems.ini";
            self::__ClientExec($cmd, $nsid);
            if (self::$__Status === true) {
                $out = self::$__Response;
                $returns = parse_ini_string($out, true);
            } else {
                admin\AdminXhtmlMsg::Echo('Warning', __METHOD__ . "->$cmd", "$nsid", self::$__ErrorMessage);
            }
        } catch (\Throwable $e) {
            admin\AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    private static function __ClientLoginCheck(): bool {
        // New
        if (self::$__Client === '') {
            $url = self::__ClientUrlGet();
            if (empty($url)) {
                self::$__Status = false;
            } else {
                require_once('inc/init.php');
                self::$__Client = new IXR_Client($url);
                self::$__Status = true;
            }
        }
        // Login
        if (!self::$__ClientLoginCheck) {
            self::__ClientExec('dokuwiki.login');
            self::$__ClientLoginCheck = (self::$__Response == '1') ? true : false;
        }
        return self::$__ClientLoginCheck;
    }
    /* -------------------------------------------------------------------- */
    private static function __ClientExec($inCmd, $inPara1 = '', $inPara2 = '', $inPara3 = '', $inPara4 = ''): void {
        $str = strtr("Remote: $inCmd($inPara1,$inPara2,$inPara3,$inPara4)", [',,' => ',', ',)' => ')']);
        self::$__Call = (substr($str, -2) == ',)') ? substr($str, 0, -2) . ')' : $str;
        self::$__Status = '';
        self::$__ErrorMessage = '';
        self::$__Response = '';
        switch ($inCmd) {
            case 'dokuwiki.login': // username, password
                break;
            case 'systems.ini';
                break;
        }
        $user = doku\DokuConf::ConfGet('plugin', 'workbook', 'connect_username');
        $pwd = doku\DokuConf::ConfGet('plugin', 'workbook', 'connect_password');
        if (empty($inPara1)) {
            self::$__Client->query($inCmd, $user, $pwd);
        } elseif (empty($inPara2)) {
            self::$__Client->query($inCmd, $user, $pwd, $inPara1);
        } elseif (empty($inPara3)) {
            self::$__Client->query($inCmd, $user, $pwd, $inPara1, $inPara2);
        } elseif (empty($inPara4)) {
            self::$__Client->query($inCmd, $user, $pwd, $inPara1, $inPara2, $inPara3);
        } else {
            self::$__Client->query($inCmd, $user, $pwd, $inPara1, $inPara2, $inPara3, $inPara4);
        }
        $out = self::$__Client->getResponse();
        self::$__Status = true;
        if (gettype($out) == 'string') {
            if (strpos($out, self::$__ErrorString) !== false) {
                self::$__Status = false;
                self::$__ErrorMessage = substr($out, strlen(self::$__ErrorString));
            }
        } elseif (gettype($out) == 'array') {
            if (isset($out['faultString'])) {
                self::$__Status = false;
                self::$__ErrorMessage = $out['faultString'];
            }
        }
        if (self::$__Status) {
            self::$__Response = $out;
        }
    }
    /* -------------------------------------------------------------------- */
    private static function __ClientUrlGet(): string {
        $return = '';
        $url = doku\DokuConf::ConfGet('plugin', 'workbook', 'connect_url');
        if (!empty($url) and filter_var($url, FILTER_VALIDATE_URL)) {
            $return = $url;
            $return .= (substr($return, -1) == '/') ? '' : '/';
            $return .= 'lib/exe/xmlrpc.php';
            return $return;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
}