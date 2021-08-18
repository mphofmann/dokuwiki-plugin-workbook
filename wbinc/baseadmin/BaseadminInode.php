<?php
namespace workbook\wbinc\baseadmin;
class BaseadminInode {
    /* -------------------------------------------------------------------- */
    public static function Clear($inPath): bool {
        self::RmR($inPath);
        self::MkdirCheck($inPath);
        return true;
    }
    /* -------------------------------------------------------------------- */
    public static function DirExec($inAction, $inDirTarget, $inDirSource, $inSourceSuffix = ''): bool {
        if ( ! is_dir($inDirSource)) return BaseadminXhtmlMsg::EchoFalse('Warning', __METHOD__, '', "Source directory doesn't exist: $inDirSource.");
        if ( ! is_dir($inDirTarget)) return BaseadminXhtmlMsg::EchoFalse('Warning', __METHOD__, '', "Target directory doesn't exist: $inDirTarget.");
        switch ($inAction) {
            case 'copy':
                self::__DirExec('copy', $inDirTarget, $inDirSource, $inSourceSuffix);
                break;
            case 'replace':
                self::__DirExec('replace', $inDirTarget, $inDirSource, $inSourceSuffix);
                break;
            default:
                BaseadminXhtmlMsg::Echo('Warning', __METHOD__, $inAction, 'Parameter unknown.');
                break;
        }
        return true;
    }
    /* -------------------------------------------------------------------- */
    public static function FileExec($inAction, $inFilepath, $inContent): bool {
        if (empty($inContent)) {
            echo BaseadminXhtml::StatusGet('red', "Content is empty [cache missing: $inFilepath]");
            return false;
        }
        if (strpos("status status-contains status-equal equal contains", $inAction) !== false and ! file_exists($inFilepath)) {
            echo BaseadminXhtml::StatusGet('orange', "File does not exist: $inFilepath]");
            return false;
        }
        $return = true;
        $cold = file_exists($inFilepath) ? trim(file_get_contents($inFilepath)) : '';
        $cnew = trim($inContent);
        switch ($inAction) {
            case 'equal':
                $return = ($cold == $cnew);
                break;
            case 'contains':
                $return = (strpos($cold, $cnew) !== false);
                break;
            case 'status':
                $status = self::FileExec('contains', $inFilepath, $cnew) ? 'yellow' : 'orange';
                $status = self::FileExec('equal', $inFilepath, $cnew) ? 'green' : $status;
                echo BaseadminXhtml::StatusGet($status, $inFilepath);
                break;
            case 'status-contains':
                $status = self::FileExec('contains', $inFilepath, $cnew) ? 'green' : 'yellow';
                echo BaseadminXhtml::StatusGet($status);
                break;
            case 'status-equal':
                $status = self::FileExec('equal', $inFilepath, $cnew) ? 'green' : 'yellow';
                echo BaseadminXhtml::StatusGet($status);
                break;
            case 'append':
                file_put_contents($inFilepath, $cold . "\n" . $cnew . "\n");
                BaseadminXhtmlMsg::Echo('Success', '', $inAction, "File appended: $inFilepath");
                break;
            case 'replace':
                file_put_contents($inFilepath, $cnew . "\n");
                BaseadminXhtmlMsg::Echo('Success', '', $inAction, "File replaced: $inFilepath");
                break;
            default:
                BaseadminXhtmlMsg::Echo('Warning', __METHOD__, $inAction, 'Parameter unknown.');
                break;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function MkdirCheck($inPath): bool {
        $return = true;
        if (is_file($inPath) or is_link($inPath)) return BaseadminXhtmlMsg::EchoFalse('Warning', __METHOD__, $inPath, 'File/Link exists already.');
        if ( ! is_dir($inPath)) {
            $return = mkdir($inPath, 02700, true);
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function MtimeInt($inPath): int {
        $inPath = (is_dir($inPath) and (substr($inPath, -1) == '/')) ? substr($inPath, 0, -1) : $inPath;
        return (int)filemtime($inPath);
    }
    /* -------------------------------------------------------------------- */
    public static function RmR($inPath): bool {
        $return = false;
        $inpath = substr($inPath, 0, strlen(WB_ROOT)) == WB_ROOT ? $inPath : WB_ROOT . $inPath;
        $paths = [//
            WB_DATACONF, //
            WB_DATAWORKBOOK, WB_ROOT . 'data/cache/', WB_ROOT . 'data/tmp/', //
            WB_ROOT . 'lib/plugins/', WB_ROOT . 'lib/tpl/', //
            WB_ROOT . 'workbook/', //
            '/ztrash/', // all trashes
        ];
        $do = false;
        foreach ($paths as $path) {
            if ((strpos($path, WB_ROOT) !== false and strpos($inpath, $path) !== false) or (strpos($inpath, $path) !== false)) {
                $do = true;
                break;
            }
        }
        if ($do) {
            system("rm -R $inpath");
            $return = true;
        } else {
            BaseadminXhtmlMsg::Echo('Warning', __METHOD__, $inPath, 'Path not allowed.');
            $return = false;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function SizeGet($inPath, $inArgs = 'bsh'): string {
        $return = shell_exec("du -{$inArgs}c $inPath | grep total");
        $return = strtr($return, ['total' => '']);
        return trim($return);
    }
    /* -------------------------------------------------------------------- */
    private static function __DirExec($inAction, $inDirTarget, $inDirSource, $inSourceSuffix = '.txt'): void {
        $inDirTarget .= (substr($inDirTarget, -1) == '/') ? '' : '/';
        $inDirSource .= (substr($inDirSource, -1) == '/') ? '' : '/';
        if (is_dir($inDirSource)) {
            foreach (scandir($inDirSource) as $inode) {
                if (substr($inode, 0, 1) == '.') continue;
                if (is_dir("{$inDirSource}{$inode}")) {
                    self::__DirExec($inAction, "{$inDirTarget}{$inode}", "{$inDirSource}{$inode}", $inSourceSuffix);
                } else {
                    $file = (substr($inode, -strlen($inSourceSuffix)) == $inSourceSuffix) ? substr($inode, 0, -strlen($inSourceSuffix)) : $inode;
                    if ($inAction == 'replace' and ! file_exists("{$inDirTarget}{$file}")) continue;
                    self::MkdirCheck($inDirTarget);
                    copy("{$inDirSource}{$inode}", "{$inDirTarget}{$file}");
                    BaseadminXhtmlMsg::Echo('Success', __METHOD__, "{$inDirTarget}{$file}", "File updated.");
                }
            }
        }
    }
    /* -------------------------------------------------------------------- */
}