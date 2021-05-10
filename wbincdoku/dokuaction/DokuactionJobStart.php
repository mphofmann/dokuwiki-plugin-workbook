<?php
namespace workbook\wbincdoku\dokuaction;
use Doku_Event;
use workbook\wbinc\action;
use workbook\wbinc\admin;
use workbook\wbincdoku\doku;
class DokuactionJobStart {
    /* -------------------------------------------------------------------- */
    public static function Event_Before_AUTH_LOGIN_CHECK_Exec(Doku_Event $Event, $inPara): void {
        if ( ! \_Wb_::ClassExists('action\ActionAction')) return;
        try {
            $Event->result = action\ActionJob::Refresh($Event->data['user'], $Event->data['password'], 'plain', $Event->data['sticky']);
            $Event->preventDefault();
            $Event->stopPropagation();
        } catch (\Throwable $e) {
            admin\AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Event_Before_AUTH_ACL_CHECK_Exec(Doku_Event $Event, $inPara): void {
        if ( ! \_Wb_::ClassExists('action\ActionAction')) return;
        try {
            $rc = action\ActionJob::AclNsidInt($Event->data['id'], $Event->data['user'], @implode(',', $Event->data['groups'])); // TODO groups might be empty
            if ($rc !== -1) {
                $Event->result = $rc;
                $Event->preventDefault();
                $Event->stopPropagation();
            }
        } catch (\Throwable $e) {
            admin\AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Event_After_DOKUWIKI_STARTED_Exec(Doku_Event $Event, $inPara): void { // forward start to e.g. start_de
        if ( ! \_Wb_::ClassExists('action\ActionAction')) return;
        try {
            action\ActionJob::StartExec();
            self::__ActionJobStartExec();
        } catch (\Throwable $e) {
            admin\AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Event_Before_ACTION_ACT_PREPROCESS_Exec(Doku_Event $Event, $inPara): void {
        if ( ! \_Wb_::ClassExists('action\ActionAction')) return;
        try {
            $Event->data = action\ActionAction::PreprocessGet($Event->data);
        } catch (\Throwable $e) {
            admin\AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Event_Before_TPL_METAHEADER_OUTPUT_Exec(Doku_Event $Event, $inPara): void {
        if ( ! \_Wb_::ClassExists('action\ActionAction')) return;
        try {
            foreach (action\ActionJob::HeadStyleHrefAr() as $href) {
                $Event->data['link'][] = ['type' => 'text/css', 'rel' => 'stylesheet', 'href' => $href];
            }
        } catch (\Throwable $e) {
            admin\AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
    }
    /* -------------------------------------------------------------------- */
    private static function __ActionJobStartExec(): void {
        if ( ! empty($_REQUEST['rev'] and @$_REQUEST['do'] != 'revert')) {
            $strdate = date('Y-m-d H:i:s', $_REQUEST['rev']);
            $href = "?id={$_REQUEST['id']}&do=revert&rev={$_REQUEST['rev']}&sectok=" . (doku\DokuXhtml::SectokGet());
            doku\DokuXhtmlMsg::Add('Notice', '', '', "Old revision: $strdate. <a href='$href'>Revert?</a>");
        }
    }
    /* -------------------------------------------------------------------- */
}