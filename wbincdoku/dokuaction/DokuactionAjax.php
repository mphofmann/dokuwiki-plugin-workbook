<?php
namespace workbook\wbincdoku\dokuaction;
use Doku_Event;
use workbook\wbinc\baseaction;
use workbook\wbincdoku\doku;
class DokuactionAjax {
    /* -------------------------------------------------------------------- */
    public static function Event_PLUGIN_MOVE_PAGE_RENAME_AfterExec(Doku_Event $Event, $inPara): void {
        if ( ! \_Wb_::RunmodeCheck('module-workbook')) return;
        try {
            if (is_array($Event->data['affected_pages'])) {
                foreach ($Event->data['affected_pages'] as $val) {
                    baseaction\BaseactionNsid::LinksRewrite($val, $Event->data['src_id'], $Event->data['dst_id']);
                }
            }
            baseaction\BaseactionNsid::MetaReset($Event->data['src_id']);
            baseaction\BaseactionNsid::MetaReset($Event->data['dst_id']);
        } catch (\Throwable $t) {
            doku\DokuAreaMsg::ThrowableAdd('Warning', $t);
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Event_TOOLBAR_DEFINE_AfterExec(Doku_Event $Event, $inPara): void {
        if ( ! \_Wb_::RunmodeCheck('module-workbook')) return;
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
}