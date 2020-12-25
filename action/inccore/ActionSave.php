<?php
namespace workbook\action\inccore;
use Doku_Event;
use workbook\wbinc\admin;
use workbookcore\wbinc\base;
class ActionSave {
    /* -------------------------------------------------------------------- */
    public static function EventBefore_COMMON_WIKIPAGE_SAVE(Doku_Event $Event, $inPara) {
        if (wb_classnsget('base\Base') == '') return;
        try {
            $out = base\BaseActionSave::BeforeExecGet($Event->data['newContent']);
            if (!empty($out)) {
                $Event->data['newContent'] = $out;
            }
        } catch (\Throwable $e) {
            admin\AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
    }
    /* -------------------------------------------------------------------- */
    public static function EventAfter_COMMON_WIKIPAGE_SAVE(Doku_Event $Event, $inPara) {
        if (wb_classnsget('base\Base') == '') return;
        try {
            base\BaseActionSave::AfterExec($Event->data['id']);
        } catch (\Throwable $e) {
            admin\AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
    }
    /* -------------------------------------------------------------------- */
}