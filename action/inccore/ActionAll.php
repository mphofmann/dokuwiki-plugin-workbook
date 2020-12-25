<?php
namespace workbook\action\inccore;
use Doku_Event;
use workbook\wbinc\admin;
use workbookcore\wbinc\base;
class ActionAll {
    /* -------------------------------------------------------------------- */
    public static function EventAfter_DOKUWIKI_STARTED(Doku_Event $Event, $inPara) { // forward start to e.g. start_de
        if (wb_classnsget('base\Base') == '') return;
        try {
            base\BaseActionAll::JobStartExec();
        } catch (\Throwable $e) {
            admin\AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
    }
    /* -------------------------------------------------------------------- */
    public static function EventBefore_AUTH_ACL_CHECK(Doku_Event $Event, $inPara) {
        if (wb_classnsget('base\Base') == '') return;
        try {
            $rc = base\BaseActionAll::AclGet($Event->data['id'], $Event->data['user'], $Event->data['groups']);
            if ($rc !== -1) {
                $Event->result = $rc;
                $Event->preventDefault();
            }
        } catch (\Throwable $e) {
            admin\AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
    }
    /* -------------------------------------------------------------------- */
    public static function EventBefore_ACTION_ACT_PREPROCESS(Doku_Event $Event, $inPara) {
        if (wb_classnsget('base\Base') == '') return;
        try {
            $Event->data = base\BaseActionAll::ActPreprocessGet($Event->data);
        } catch (\Throwable $e) {
            admin\AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
    }
    /* -------------------------------------------------------------------- */
    public static function EventBefore_TPL_METAHEADER_OUTPUT(Doku_Event $Event, $inPara) {
        if (wb_classnsget('base\Base') == '') return;
        try {
            $Event->data['link'][] = ['type' => 'text/css', 'rel' => 'stylesheet', 'href' => base\BaseConf::ConstGet('WB_URLPATH') . 'lib/plugins/workbookcore/wbassets/fontawesome/free-5.15.1-web/css/all.css'];
        } catch (\Throwable $e) {
            admin\AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
    }
    /* -------------------------------------------------------------------- */
    public static function EventAfter_DOKUWIKI_DONE(Doku_Event $Event, $inPara) { //
        if (wb_classnsget('base\Base') == '') return;
        try {
            base\BaseActionAll::JobEndExec();
        } catch (\Throwable $e) {
            admin\AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
    }
    /* -------------------------------------------------------------------- */
}