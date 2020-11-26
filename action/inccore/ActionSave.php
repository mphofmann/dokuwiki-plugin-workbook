<?php
namespace workbook\action\inccore;
use Doku_Event;
use workbookcore\wbinc\base;
class ActionSave {
    /* -------------------------------------------------------------------- */
    public static function EventBefore_COMMON_WIKIPAGE_SAVE(Doku_Event $Event, $inPara) {
        $out = base\BaseActionSave::Before_COMMON_WIKIPAGE_SAVE($Event->data['newContent']);
        if (!empty($out)) {
            $Event->data['newContent'] = $out;
        }
    }
    /* -------------------------------------------------------------------- */
    public static function EventAfter_COMMON_WIKIPAGE_SAVE(Doku_Event $Event, $inPara) {
        base\BaseActionSave::After_COMMON_WIKIPAGE_SAVE($Event->data['id']);
    }
    /* -------------------------------------------------------------------- */
}