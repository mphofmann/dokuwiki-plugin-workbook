<?php
namespace workbook\wbinc\baseadmin;
class BaseadminRemote {
    /* -------------------------------------------------------------------- */
    private static $__SystemsAr = [];
    private static $__UrlMtimeAr = [];
    /* -------------------------------------------------------------------- */
    public static function EnabledCheck($inType = ''): bool {
        $_strpara = empty($inType) ? '' : ' type=login';
        return (bool)\_Wb_::CmdExec("sys\SysRemote::EnabledCheck{$_strpara}") ?? false;
    }
    /* -------------------------------------------------------------------- */
    public static function Exec($inAction) {
        $status = (self::EnabledCheck()) ? 'green' : 'red';
        switch ($inAction) {
            case 'status':
                echo BaseadminXhtml::StatusGet($status);
                break;
            case 'login':
                if (self::EnabledCheck('login')) {
                    BaseadminXhtmlMsg::Echo('Success', __METHOD__, '', 'Remote login successful.');
                } else {
                    BaseadminXhtmlMsg::Echo('Warning', __METHOD__, '', 'Remote login failed.');
                }
                break;
            default:
                BaseadminXhtmlMsg::Echo('Warning', __METHOD__, $inAction, 'Parameter unknown.');
                break;
        }
    }
    /* -------------------------------------------------------------------- */
    public static function ExtensionAr($inType, $inExtension): array {
        $returns = [];
        foreach (self::$__SystemsAr as $id => $ar) {
            if (strpos($id, $inType) === false) continue;
            if (isset($ar[$inExtension])) {
                $returns = $ar[$inExtension];
                break;
            }
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    public static function IpMyGet(): string {
        return file_get_contents("http://checkip.amazonaws.com");
    }
    /* -------------------------------------------------------------------- */
    public static function NoteGet(): string {
        $return = '<table style="white-space:nowrap; border:1px solid #ccc; font-size:smaller;">';
        $ar = ['connect_url' => '', 'connect_username' => '', 'connect_password' => '', 'connect_mail' => '', 'connect_terms' => '', 'connect_dist' => '', 'connect_version' => ''];
        foreach ($ar as $id => $val) $ar[$id] = BaseadminConf::Get('plugin', 'workbook', $id);
        $myip = self::IpMyGet();
        $myurl = self::UrlMyGet();
        $return .= "<tr><td>URL:</td><td>{$ar['connect_url']}</td></tr>";
        $return .= "<tr><td>Login:</td><td>{$ar['connect_username']} " . str_pad('', strlen($ar['connect_password']), '*') . "</td></tr>";
        $return .= "<tr><td>Mail:</td><td>{$ar['connect_mail']}</td></tr>";
        $return .= "<tr><td>Terms:</td><td>{$ar['connect_terms']}</td></tr>";
        $return .= "<tr><td>Distribution:</td><td>{$ar['connect_dist']}</td></tr>";
        $return .= "<tr><td>My IP:</td><td>{$myip}</td></tr>";
        $return .= "<tr><td>My URL:</td><td>{$myurl}</td></tr>";
        $return .= '</table>';
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function SystemsAr($inExtension = ''): array {
        if (empty(self::$__SystemsAr)) {
            if (@filemtime('workbook/cache/systems.ini') < filemtime('conf/local.php')) {
                $ar = \_Wb_::CmdExec("sys\SysRemote::SystemsAr") ?? [];
                file_put_contents('workbook/cache/systems.ini', serialize($ar));
            }
            self::$__SystemsAr = (array)unserialize(file_get_contents('workbook/cache/systems.ini'));
        }
        return self::$__SystemsAr;
    }
    /* -------------------------------------------------------------------- */
    public static function UrlCacheFilepathGet($inUrl): string {
        if ( ! self::UrlExists($inUrl)) return '';
        $basename = basename($inUrl);
        $filepath = BaseadminCache::FilepathGet(__NAMESPACE__, 'download', $basename);
        $srcmtime = self::UrlMtime($inUrl);
        $download = false;
        if ( ! file_exists($filepath)) $download = true;
        if ($srcmtime == 0) $download = true;
        if (@filemtime($filepath) < $srcmtime) $download = true;
        if ($download) {
            $str = file_get_contents($inUrl);
            if (strlen($str) > 0) {
                BaseadminCache::Put(__NAMESPACE__, 'download', $basename, $str);
                BaseadminXhtmlMsg::Echo('Notice', '', '', "Source downloaded: $inUrl");
            }
        }
        return file_exists($filepath) ? $filepath : '';
    }
    /* -------------------------------------------------------------------- */
    public static function UrlExists($inUrl): bool {
        if (filter_var($inUrl, FILTER_VALIDATE_URL) === false) return 0;
        $headers = @get_headers($inUrl);
        return ( ! $headers or strpos($headers[0], '404') !== false) ? false : true;
    }
    /* -------------------------------------------------------------------- */
    public static function UrlMtime($inUrl): int {
        if (filter_var($inUrl, FILTER_VALIDATE_URL) === false) return 0;
        if ( ! isset(self::$__UrlMtimeAr[$inUrl])) {
            $ar = get_headers($inUrl);
            if (is_array($ar)) {
                foreach ($ar as $val) {
                    if (stripos($val, "Last-Modified:") !== false) {
                        list($str, $date) = explode(':', $val, 2);
                        self::$__UrlMtimeAr[$inUrl] = strtotime($date);
                        break;
                    }
                }
            }
        }
        return self::$__UrlMtimeAr[$inUrl] ?? 0;
    }
    /* -------------------------------------------------------------------- */
    public static function UrlMyGet(): string {
        $urlself = (isset($_SERVER['HTTPS']) && @$_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        $ar = parse_url($urlself);
        $return = $ar['host'] . $ar['path'];
        if (substr($return, -1) !== '/') $return = substr($return, 0, strrpos($return, '/') + 1);
        return $return;
    }
    /* -------------------------------------------------------------------- */
}