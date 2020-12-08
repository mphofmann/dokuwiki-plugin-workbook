<?php
namespace workbook\action\inccore;
use Doku_Event;
use JSON;
use workbookcore\wbinc\base;
use workbookcore\wbinc\env;
use workbookcore\wbinc\util;
class ActionAjax {
    /* -------------------------------------------------------------------- */
    public static function EventAfter_TOOLBAR_DEFINE(Doku_Event $Event, $inPara) {
        if (workbookclassnsget('base\Base')=='') return;
        // Plugins
        $adds = [];
        foreach (scandir(DOKU_INC . 'lib/plugins/') as $plugin) {
            if (substr($plugin, 0, strlen('workbook')) != 'workbook') continue;
            if (is_dir(DOKU_INC . "lib/plugins/$plugin/wbdef/wb/")) {
                foreach (scandir(DOKU_INC . "lib/plugins/$plugin/wbdef/wb/") as $inode) {
                    if (substr($inode, 0, 3) != 'Wb_') continue;
                    $class = strtr($inode, ['Wb_' => '', '.php' => '']);
                    $classpathclass = base\Base::ClassNsGet("wb\\Wb_$class") . "wb\\Wb_$class";
                    list($type, $icon) = explode(' ', $classpathclass::ClassIconGet(), 2);
                    if (!empty($icon)) {
                        $dirs = ['fas' => 'solid', 'far' => 'regular', 'fab' => 'brands'];
                        $icondir = $dirs[$type];
                        $iconsvg = substr($icon, 3);
                        $strattr = strtolower($class) . " div-mode=help";
                        $strinsert = (substr($class, 0, strlen('Block')) == 'Block') ? "<wb $strattr>?</wb>" : "<wb $strattr/>";
                        $adds[$class] = ['type' => 'insert', 'icon' => "../../plugins/workbookcore/wbassets/fontawesome/free-5.15.1-web/svgs/$icondir/$iconsvg.svg", 'title' => $class, 'insert' => $strinsert, 'block' => true,];
                    }
                }
            }
        }
        if (!empty($adds)) {
            ksort($adds);
            $adds = array_values($adds);
            $Event->data['workbook'] = [ //
                'type' => 'picker', //
                'title' => 'Workbook', //
                'icon' => '../../plugins/workbookcore/wbassets/fontawesome/free-5.15.1-web/svgs/solid/cubes.svg', //
                'list' => $adds, //
            ];
        }
        // Do_
        if (class_exists('workbookdo\wbdef\wb\Wb_Do')) $Event->data['workbook-do'] = ['type' => 'format', 'title' => 'Do-item', 'icon' => '../../plugins/workbookcore/wbassets/fontawesome/free-5.15.1-web/svgs/regular/check-square.svg', 'key' => 't', 'open' => "<wb do !? ?! ?h @?>", 'close' => '</wb>', 'block' => false,];
    }
    /* -------------------------------------------------------------------- */
    public static function EventBefore_AJAX_CALL_UNKNOWN(Doku_Event $Event, $inPara) {
        if (workbookclassnsget('base\Base')=='') return;
        global $ID, $conf, $lang;
        if ($Event->data !== 'plugin_workbook_do') {
            return;
        }
        // No other ajax call handlers needed
        $Event->stopPropagation();
        $Event->preventDefault();
        // Inputs
        if (isset($_REQUEST['index'], $_REQUEST['checked'], $_REQUEST['nsid'])) {
            $index = $_REQUEST['index'];
            $checked = (boolean)urldecode($_REQUEST['checked']);
            $ID = cleanID(urldecode($_REQUEST['nsid']));
        } else {
            return;
        }
        $date = 0;
        if (isset($_REQUEST['date'])) $date = (int)$_REQUEST['date'];
        $INFO = pageinfo();
        // Check ACL
        if (auth_quickaclcheck($ID) < base\BaseAcl::$Consts['AUTH_EDIT']) {
            echo "You do not have permission to edit this file.\nAccess was denied.";
            return;
        }
        // Check, if page is locked
        if (checklock($ID)) {
            $locktime = filemtime(wikiLockFN($ID));
            $expire = dformat($locktime + $conf['locktime']);
            $min = round(($conf['locktime'] - (time() - $locktime)) / 60);
            $msg = "Page locked." . "\n" . $lang['lockedby'] . ': ' . editorinfo($INFO['locked']) . "\n" . $lang['lockexpire'] . ': ' . $expire . ' (' . $min . ' min)';
            self::__JsonPrint(array('message' => $msg));
            return;
        }
        // Conflict check
        if ($date != 0 and $INFO['meta']['date']['modified'] > $date) {
            self::__JsonPrint(array('message' => "Refresh the page."));
            return;
        }
        // Modify
        $wikitext = rawWiki($ID);
        preg_match_all('/<wb do.*?>.*?<\/wb>/', $wikitext, $matches);
        foreach ($matches[0] as $match) {
            $data = util\UtilSyntax::HandleAr($match);
            if ($index == base\Base::IndexGet($data['match'])) {
                if ($checked == 1) {
                    $pos = strpos($match, '>');
                    $user = env\EnvUserCurrent::Get();
                    $matchnew = trim(substr($match, 0, $pos)) . " #$user=" . date('Y-m-d') . substr($match, $pos);
                } else {
                    $pieces = explode(' ', $match);
                    foreach ($pieces as $id => $val) {
                        if (substr($val, 0, 1) == '#') {
                            if (($pos = strpos($val, '>')) === false) {
                                unset($pieces[$id]);
                            } else {
                                $pieces[$id] = substr($val, $pos);
                            }
                        }
                    }
                    $matchnew = implode(' ', $pieces);
                }
                $wikitextnew = strtr($wikitext, [$match => $matchnew]);
                // Save
                lock($ID);
                saveWikiText($ID, $wikitextnew, ($checked ? 'wbdo-checked=1' : 'wbdo-checked=0'), true);
                unlock($ID);
                $return = array('date' => @filemtime(wikiFN($ID)), 'succeed' => true);
                self::__JsonPrint($return);
                break;
            }
        }
    }
    /* -------------------------------------------------------------------- */
    public static function EventAfter_PLUGIN_MOVE_PAGE_RENAME(Doku_Event $Event, $inPara) {
        if (workbookclassnsget('base\Base')=='') return;
        if (is_array($Event->data['affected_pages'])) {
            foreach ($Event->data['affected_pages'] as $val) {
                base\BaseNsid::LinksRewrite($val, $Event->data['src_id'], $Event->data['dst_id']);
            }
        }
        base\BaseNsid::MetaReset($Event->data['src_id']);
        base\BaseNsid::MetaReset($Event->data['dst_id']);
    }
    /* -------------------------------------------------------------------- */
    private static function __JsonPrint($return) {
        $json = new JSON();
        echo $json->encode($return);
    }
    /* -------------------------------------------------------------------- */
}