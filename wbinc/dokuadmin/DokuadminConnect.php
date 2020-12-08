<?php
namespace workbook\wbinc\dokuadmin;
use IXR_Client;
use workbook\wbinc\admin;
use workbook\wbinc\doku;
class DokuadminConnect {
    /* -------------------------------------------------------------------- */
    private static $__Client = '';
    private static $__ClientLogin = '';
    private static $__Call = '';
    private static $__Status = '';
    private static $__Response = '';
    private static $__ErrorString = 'WB-EXCEPTION: ';
    private static $__ErrorMessage = '';
    /* -------------------------------------------------------------------- */
    public static function Action($inAction) {
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
    public static function NoteGet() {
        $return = '<table style="white-space:nowrap; border:1px solid #ccc; font-size:smaller;">';
        $url = doku\DokuGlobal::ConfGet('plugin', 'workbook', 'connect_url');
        $username = doku\DokuGlobal::ConfGet('plugin', 'workbook', 'connect_username');
        $password = str_pad('', strlen(doku\DokuGlobal::ConfGet('plugin', 'workbook', 'connect_password')), '*');
        $mail = doku\DokuGlobal::ConfGet('plugin', 'workbook', 'connect_mail');
        $myip = admin\AdminUtil::IpPublicGet();
        $return .= "<tr><td>Url:</td><td>{$url}</td></tr>";
        $return .= "<tr><td>Login:</td><td>{$username} {$password}</td></tr>";
        $return .= "<tr><td>Mail:</td><td>{$mail}</td></tr>";
        $return .= "<tr><td>My IP:</td><td>{$myip}</td></tr>";
        $return .= '</table>';
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function EnabledCheck($inType = '') {
        if (self::$__ClientLogin === true) return true;
        $url = doku\DokuGlobal::ConfGet('plugin', 'workbook', 'connect_url');
        $username = doku\DokuGlobal::ConfGet('plugin', 'workbook', 'connect_username');
        $password = doku\DokuGlobal::ConfGet('plugin', 'workbook', 'connect_password');
        $mail = doku\DokuGlobal::ConfGet('plugin', 'workbook', 'connect_mail');
        if (empty($url) or !filter_var($url, FILTER_VALIDATE_URL)) return false;
        if (empty($username) or $username == '!!not set!!') return false;
        if (empty($password) or $password == '!!not set!!') return false;
        if (empty($mail) or $mail == '!!not set!!') return false;
        $return = true;
        if ($inType == 'login') {
            $return = self::__ClientLogin();
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function SystemsAr() {
        $returns = [];
        try {
            if (!self::EnabledCheck('login')) return false;
            $cmd = "plugin.workbookhub.SysDataIdContentsGet";
            $version = doku\DokuGlobal::ConfGet('plugin', 'workbook', 'connect_version');
            $nsid = "zsync:sync:$version:conf:systems.ini";
            self::__ClientCall($cmd, $nsid);
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
    private static function __ClientLogin() {
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
        if (self::$__ClientLogin === '') {
            self::__ClientCall('dokuwiki.login');
            self::$__ClientLogin = (self::$__Response == '1') ? true : false;
        }
        return self::$__ClientLogin;
    }
    /* -------------------------------------------------------------------- */
    private static function __ClientCall($inCmd, $inPara1 = '', $inPara2 = '', $inPara3 = '', $inPara4 = '') {
        $str = strtr("Remote: $inCmd($inPara1,$inPara2,$inPara3,$inPara4)", [',,' => ',', ',)' => ')']);
        self::$__Call = (substr($str, -2) == ',)') ? substr($str, 0, -2) . ')' : $str;
        self::$__Status = '';
        self::$__ErrorMessage = '';
        self::$__Response = '';
        switch ($inCmd) {
            case 'dokuwiki.login': // username, password
                $inPara1 = doku\DokuGlobal::ConfGet('plugin', 'workbook', 'connect_username');
                $inPara2 = doku\DokuGlobal::ConfGet('plugin', 'workbook', 'connect_password');
                break;
            case 'systems.ini';
                break;
        }
        if (empty($inPara1)) {
            self::$__Client->query($inCmd);
        } elseif (empty($inPara2)) {
            self::$__Client->query($inCmd, $inPara1);
        } elseif (empty($inPara3)) {
            self::$__Client->query($inCmd, $inPara1, $inPara2);
        } elseif (empty($inPara4)) {
            self::$__Client->query($inCmd, $inPara1, $inPara2, $inPara3);
        } else {
            self::$__Client->query($inCmd, $inPara1, $inPara2, $inPara3, $inPara4);
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
    private static function __ClientUrlGet() {
        $return = '';
        $url = doku\DokuGlobal::ConfGet('plugin', 'workbook', 'connect_url');
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