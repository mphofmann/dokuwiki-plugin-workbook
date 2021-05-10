<?php
namespace workbook\wbincdoku\dokuaction;
use Doku_Event;
use workbook\wbinc\action;
use workbook\wbinc\admin;
class DokuactionAjax {
    /* -------------------------------------------------------------------- */
    public static function Event_After_TOOLBAR_DEFINE_Exec(Doku_Event $Event, $inPara): void {
        if ( ! \_Wb_::ClassExists('action\ActionAction')) return;
        // Do_
        if (file_exists(DOKU_INC . 'workbook/module/workbookdo/wbdef/wb/Wb_Do.php')) $Event->data['workbook-do'] = ['type' => 'format', 'title' => 'Do-item', 'icon' => '../../../workbook/module/workbook/wbasset/fontawesome/free-5.15.1-web/svgs/regular/check-square.svg', 'key' => 't', 'open' => "<wb do !? ?! ?h @?>", 'close' => '</wb>', 'block' => false,];
        /* Module
        $adds = [];
        foreach (scandir(DOKU_INC . 'workbook/module/') as $module) {
            if (is_dir(DOKU_INC . "workbook/module/$module/wbdef/wb/")) {
                foreach (scandir(DOKU_INC . "workbook/module/$module/wbdef/wb/") as $inode) {
                    if (substr($inode, 0, 3) != 'Wb_') continue;
                    $class = strtr($inode, ['Wb_' => '', '.php' => '']);
                    $classpathclass = \_Wb_::ClassNsGet("wb\\Wb_$class") . "wb\\Wb_$class";
                    list($type, $icon) = explode(' ', $classpathclass::ClassIconGet(), 2);
                    if (!empty($icon)) {
                        $dirs = ['fas' => 'solid', 'far' => 'regular', 'fab' => 'brands'];
                        $icondir = $dirs[$type];
                        $iconsvg = substr($icon, 3);
                        $strattr = strtolower($class) . " div-mode=help";
                        $strinsert = (substr($class, 0, strlen('Block')) == 'Block') ? "<wb $strattr>?</wb>" : "<wb $strattr/>";
                        $adds[$class] = ['type' => 'insert', 'icon' => "../../../workbook/module/workbook/wbasset/fontawesome/free-5.15.1-web/svgs/$icondir/$iconsvg.svg", 'title' => $class, 'insert' => $strinsert, 'block' => true,];
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
                'icon' => '../../../workbook/module/workbook/wbasset/fontawesome/free-5.15.1-web/svgs/solid/cubes.svg', //
                'list' => $adds, //
            ];
        } */
    }
    /* -------------------------------------------------------------------- */
    public static function Event_After_PLUGIN_MOVE_PAGE_RENAME_Exec(Doku_Event $Event, $inPara): void {
        if ( ! \_Wb_::ClassExists('action\ActionAction')) return;
        try {
            if (is_array($Event->data['affected_pages'])) {
                foreach ($Event->data['affected_pages'] as $val) {
                    action\ActionNsid::LinksRewrite($val, $Event->data['src_id'], $Event->data['dst_id']);
                }
            }
            action\ActionNsid::MetaReset($Event->data['src_id']);
            action\ActionNsid::MetaReset($Event->data['dst_id']);
        } catch (\Throwable $e) {
            admin\AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
    }
    /* -------------------------------------------------------------------- */
}