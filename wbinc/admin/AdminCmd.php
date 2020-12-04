<?php
namespace workbook\wbinc\admin;
class AdminCmd {
    /* -------------------------------------------------------------------- */
    public static function SystemEcho($inCmd, $showCmd = true) {
        if ($showCmd) echo "$inCmd\n";
        return system($inCmd);
    }
    /* -------------------------------------------------------------------- */
    public static function SystemGet($inCmd, $showCmd = false) {
        $return = '';
        if ($showCmd) $return .= "$inCmd\n";
        $return .= shell_exec($inCmd);
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function ExecGet($inCmd, $inValue = '') {
        $return = '';
        // Paras
        $paras = self::__ParaGet($inCmd);
        if (!empty($inValue)) {
            $paras['value'] = $inValue;
            if (strpos($inValue, '=') !== false) {
                $pieces = explode('=', $inValue);
                $paras[$pieces[0]] = $pieces[1];
            }
        }
        // Exec
        $class = $paras['class'];
        $method = $paras['method'];
        $classpathclass = workbookclassnsget($class) . $class;
        if (!class_exists($classpathclass)) {
            $return .= "Error: Class unknown: $inCmd ($class)";
            return $return;
        }
        if (!method_exists($classpathclass, $method)) {
            $return .= "Error: Method unknown: $inCmd ($class::$method)";
            return $return;
        }
        unset($paras['class'], $paras['method']);
        ob_start();
        $out = '';
        if ($method == 'Action') {
            $out = $classpathclass::$method($paras);
        } else {
            switch (count($paras)) {
                case 0:
                    $out = $classpathclass::$method();
                    break;
                case 1:
                    $var1 = array_shift($paras);
                    $out = $classpathclass::$method($var1);
                    break;
                case 2:
                    $var1 = array_shift($paras);
                    $var2 = array_shift($paras);
                    $out = $classpathclass::$method($var1, $var2);
                    break;
                case 3:
                    $var1 = array_shift($paras);
                    $var2 = array_shift($paras);
                    $var3 = array_shift($paras);
                    $out = $classpathclass::$method($var1, $var2, $var3);
                    break;
                case 4:
                    $var1 = array_shift($paras);
                    $var2 = array_shift($paras);
                    $var3 = array_shift($paras);
                    $var4 = array_shift($paras);
                    $out = $classpathclass::$method($var1, $var2, $var3, $var4);
                    break;
                case 5:
                    $var1 = array_shift($paras);
                    $var2 = array_shift($paras);
                    $var3 = array_shift($paras);
                    $var4 = array_shift($paras);
                    $var5 = array_shift($paras);
                    $out = $classpathclass::$method($var1, $var2, $var3, $var4, $var5);
                    break;
            }
        }
        echo $out;
        $return .= ob_get_clean();
        return $return;
    }
    /* -------------------------------------------------------------------- */
    private static function __ParaGet($inString) {
        $pieces = explode(' ', $inString);
        if (strpos($inString, '::') === false) {
            $return['class'] = 'wbadmincmd_' . @array_shift($pieces);
            $return['method'] = @array_shift($pieces);
        } else {
            $str = @array_shift($pieces);
            list($return['class'], $return['method']) = explode('::', $str);
        }
        foreach ($pieces as $val) {
            $p = explode('=', $val, 2);
            $return[$p[0]] = $p[1];
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
}