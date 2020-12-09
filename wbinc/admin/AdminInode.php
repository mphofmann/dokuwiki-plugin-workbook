<?php
namespace workbook\wbinc\admin;
class AdminInode {
    /* -------------------------------------------------------------------- */
    public static function MkdirCheck($inPath) {
        if (is_file($inPath)) return AdminXhtmlMsg::Echo('Warning', __METHOD__, $inPath, 'File exists already.');
        if (!is_dir($inPath)) {
            mkdir($inPath, 02700, true);
        }
    }
    /* -------------------------------------------------------------------- */
    public static function RmR($inPath) {
        if (substr($inPath, 0, 1) == '/') return AdminXhtmlMsg::Echo('Warning', __METHOD__, $inPath, 'Absolute paths not allowed.');
        $paths = [//
            'conf/lang/', 'conf/plugin/', 'conf/plugin_lang/', //
            'data/cache/', 'data/tmp/', 'data/workbook/sync/', 'data/workbook/tmp/',  //
            'lib/plugins/', 'lib/tpl/', //
            '/ztrash/', // all trashes
        ];
        $do = false;
        foreach ($paths as $path) {
            if (strpos($inPath, $path) !== false) {
                $do = true;
                break;
            }
        }
        if ($do) {
            system("rm -R {$inPath}");
        } else {
            AdminXhtmlMsg::Echo('Warning', __METHOD__, $inPath, 'Path not allowed.');
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Clear($inPath) {
        self::RmR($inPath);
        self::MkdirCheck($inPath);
    }
    /* -------------------------------------------------------------------- */
    public static function SizeGet($inPath, $inArgs = 'bsh') {
        $return = shell_exec("du -{$inArgs}c $inPath | grep total");
        $return = strtr($return, ['total' => '']);
        return trim($return);
    }
    /* -------------------------------------------------------------------- */
    public static function DirAction($inAction, $inDirTarget, $inDirSource, $inSourceSuffix = '') {
        if (!is_dir($inDirSource)) return AdminXhtmlMsg::Echo('Warning', __METHOD__, '', "Source directory doesn't exist: $inDirSource.");
        if (!is_dir($inDirTarget)) return AdminXhtmlMsg::Echo('Warning', __METHOD__, '', "Target directory doesn't exist: $inDirTarget.");
        $return = '';
        switch ($inAction) {
            case 'copy':
                self::__DirActionExec('copy', $inDirTarget, $inDirSource, $inSourceSuffix);
                break;
            case 'replace':
                self::__DirActionExec('replace', $inDirTarget, $inDirSource, $inSourceSuffix);
                break;
            default:
                AdminXhtmlMsg::Echo('Warning', __METHOD__, $inAction, 'Parameter unknown.');
                break;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function FileAction($inAction, $inFilepath, $inContent) {
        if (empty($inContent)) {
            echo AdminXhtml::StatusGet('red', "Content is empty [cache missing: $inFilepath]");
            return '';
        }
        if (strpos("status status-contains status-equal equal contains", $inAction) !== false and !file_exists($inFilepath)) {
            echo AdminXhtml::StatusGet('orange', "File does not exist: $inFilepath]");
            return '';
        }
        $return = '';
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
                $status = self::FileAction('contains', $inFilepath, $cnew) ? 'yellow' : 'orange';
                $status = self::FileAction('equal', $inFilepath, $cnew) ? 'green' : $status;
                echo AdminXhtml::StatusGet($status, $inFilepath);
                break;
            case 'status-contains':
                $status = self::FileAction('contains', $inFilepath, $cnew) ? 'green' : 'yellow';
                echo AdminXhtml::StatusGet($status);
                break;
            case 'status-equal':
                $status = self::FileAction('equal', $inFilepath, $cnew) ? 'green' : 'yellow';
                echo AdminXhtml::StatusGet($status);
                break;
            case 'append':
                file_put_contents($inFilepath, $cold . "\n" . $cnew . "\n");
                break;
            case 'replace':
                file_put_contents($inFilepath, $cnew . "\n");
                break;
            default:
                AdminXhtmlMsg::Echo('Warning', __METHOD__, $inAction, 'Parameter unknown.');
                break;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    private static function __DirActionExec($inAction, $inDirTarget, $inDirSource, $inSourceSuffix = '.txt') {
        $inDirTarget .= (substr($inDirTarget, -1) == '/') ? '' : '/';
        $inDirSource .= (substr($inDirSource, -1) == '/') ? '' : '/';
        if (is_dir($inDirSource)) {
            foreach (scandir($inDirSource) as $inode) {
                if (substr($inode, 0, 1) == '.') continue;
                if (is_dir("{$inDirSource}{$inode}")) {
                    self::__DirActionExec($inAction, "{$inDirTarget}{$inode}", "{$inDirSource}{$inode}", $inSourceSuffix);
                } else {
                    $file = (substr($inode, -strlen($inSourceSuffix)) == $inSourceSuffix) ? substr($inode, 0, -strlen($inSourceSuffix)) : $inode;
                    if ($inAction == 'replace' and !file_exists("{$inDirTarget}{$file}")) continue;
                    self::MkdirCheck($inDirTarget);
                    copy("{$inDirSource}{$inode}", "{$inDirTarget}{$file}");
                    AdminXhtmlMsg::Echo('Success', __METHOD__, "{$inDirTarget}{$file}", "File updated.");
                }
            }
        }
    }
    /* -------------------------------------------------------------------- */
}