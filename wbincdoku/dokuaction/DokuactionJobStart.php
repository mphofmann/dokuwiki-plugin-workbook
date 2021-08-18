<?php
namespace workbook\wbincdoku\dokuaction;
use Doku_Event;
use workbook\wbinc\baseaction;
use workbook\wbincdoku\doku;
class DokuactionJobStart {
    /* -------------------------------------------------------------------- */
    public static function Event_AUTH_ACL_CHECK_AfterExec(Doku_Event $Event, $inPara): void {
        // TODO Dokuwiki Bug?
        $act = doku\DokuGlobal::ActGet();
        if (empty($act)) return; // AJAX
        if (strpos('index recent save', $act) !== false) return;
        if ($Event->result === 0 and $act != 'denied') {
            send_redirect("doku.php?do=denied&id={$_REQUEST['id']}");
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Event_AUTH_ACL_CHECK_BeforeExec(Doku_Event $Event, $inPara): void {
        if ( ! \_Wb_::RunmodeCheck('module-workbook')) return;
        try {
            if (doku\DokuGlobal::ActGet() == '' and empty($Event->data['id'])) { // AJAX inline editor for Global-Table
                $rc = AUTH_EDIT;
            } else {
                $rc = baseaction\BaseactionJob::AclNsidInt($Event->data['id'], $Event->data['user'], @implode(',', $Event->data['groups'])); // TODO groups might be empty
            }
            if ($rc !== -1) {
                $Event->result = $rc;
                $Event->preventDefault();
                $Event->stopPropagation();
            }
        } catch (\Throwable $t) {
            doku\DokuAreaMsg::ThrowableAdd('Warning', $t);
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Event_AUTH_LOGIN_CHECK_BeforeExec(Doku_Event $Event, $inPara): void {
        if ( ! \_Wb_::RunmodeCheck('module-workbook')) return;
        try {
            $Event->result = baseaction\BaseactionJob::Refresh($Event->data['user'], $Event->data['password'], 'plain', $Event->data['sticky']);
            $Event->preventDefault();
            $Event->stopPropagation();
            global $USERINFO;
            if (empty($USERINFO)) $USERINFO = $_SESSION[DOKU_COOKIE]['auth']['info'];
        } catch (\Throwable $t) {
            doku\DokuAreaMsg::ThrowableAdd('Warning', $t);
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Event_DOKUWIKI_STARTED_AfterExec(Doku_Event $Event, $inPara): void { // forward start to e.g. start_de
        if ( ! \_Wb_::RunmodeCheck('module-workbook')) return;
        try {
            baseaction\BaseactionJob::JobStartExec();
            self::__ActionJobStartExec();
        } catch (\Throwable $t) {
            doku\DokuAreaMsg::ThrowableAdd('Warning', $t);
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Event_MAIL_MESSAGE_SEND_BeforeExec(Doku_Event $Event, $inPara): void {
        if ( ! \_Wb_::RunmodeCheck('module-workbook')) return;
        try {
            // $Event->data['from'] not, respectively set automatically
            $Event->result = baseaction\BaseactionJob::MailSend($Event->data['to'], $Event->data['subject'], $Event->data['body'], $Event->data['cc'], $Event->data['bcc'], '', $Event->data['headers'], $Event->data['params']);
            $Event->preventDefault();
            $Event->stopPropagation();
        } catch (\Throwable $t) {
            doku\DokuAreaMsg::ThrowableAdd('Warning', $t);
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Event_TPL_METAHEADER_OUTPUT_BeforeExec(Doku_Event $Event, $inPara): void {
        if ( ! \_Wb_::RunmodeCheck('module-workbook')) return;
        try {
            foreach (baseaction\BaseactionJob::HeadStyleHrefAr() as $href) {
                $Event->data['link'][] = ['type' => 'text/css', 'rel' => 'stylesheet', 'href' => $href];
            }
        } catch (\Throwable $t) {
            doku\DokuAreaMsg::ThrowableAdd('Warning', $t);
        }
    }
    /* -------------------------------------------------------------------- */
    private static function __ActionJobStartExec(): void {
        if ( ! empty($_REQUEST['do']) and @strpos('revert edit', @$_REQUEST['do']) !== false) return;
        if ( ! empty($_REQUEST['rev'])) {
            $strdate = date('Y-m-d H:i:s', $_REQUEST['rev']);
            $href = "?id={$_REQUEST['id']}&do=revert&rev={$_REQUEST['rev']}&sectok=" . (doku\DokuXhtml::SectokGet());
            doku\DokuAreaMsg::Add('Notice', '', '', "Old revision: $strdate. <a href='$href'>Revert?</a>");
        }
    }
    /* -------------------------------------------------------------------- */
}