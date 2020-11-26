<?php
namespace workbook\action;
use Doku_Event;
use Doku_Event_Handler;
use DokuWiki_Action_Plugin;
abstract class a_Action extends DokuWiki_Action_Plugin {
    /* -------------------------------------------------------------------- */
    protected $_Events = [];
    /* -------------------------------------------------------------------- */
    public function register(Doku_Event_Handler $Controller) {
        foreach ($this->_Events as $act => $ar) {
            foreach ($ar as $id => $val) {
                $Controller->register_hook($id, 'BEFORE', $this, 'handle_event_before');
                $Controller->register_hook($id, 'AFTER', $this, 'handle_event_after');
            }
        }
    }
    /* -------------------------------------------------------------------- */
    public function handle_event_before(Doku_Event $Event, $inPara) {
        $this->_Exec('before', $Event, $inPara);
    }
    /* -------------------------------------------------------------------- */
    public function handle_event_after(Doku_Event $Event, $inPara) {
        $this->_Exec('after', $Event, $inPara);
    }
    /* -------------------------------------------------------------------- */
    protected function _Exec($inType, Doku_Event $Event, $inPara) {
        if (strpos(constant('WB_RUNMODE'), 'workbookcore-ok') === false) return;
        global $ACT;
        foreach (['all', 'ajax', @$ACT] as $act) {
            if (@isset($this->_Events[$act][$Event->name])) {
                $classpathclass = $this->_Events[$act][$Event->name];
                $method = 'Event' . ucfirst($inType) . '_' . $Event->name;
                if (class_exists($classpathclass)) {
                    if (method_exists($classpathclass, $method)) {
                        $classpathclass::$method($Event, $inPara);
                    }
                }
            }
        }
    }
    /* -------------------------------------------------------------------- */
}