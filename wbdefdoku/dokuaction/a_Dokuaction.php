<?php
namespace workbook\wbdefdoku\dokuaction;
use Doku_Event;
use Doku_Event_Handler;
use DokuWiki_Action_Plugin;
use Throwable;
use workbook\wbincdoku\doku;
abstract class a_Dokuaction extends DokuWiki_Action_Plugin {
    /* -------------------------------------------------------------------- */
    protected $_Events = [];
    /* -------------------------------------------------------------------- */
    protected function _Exec($inType, Doku_Event $Event, $inPara) {
        global $ACT;
        $eventname = $Event->name;
        foreach (['JobStart', 'Ajax', @ucfirst($ACT), 'JobEnd'] as $act) {
            if (@isset($this->_Events[$act][$eventname])) {
                $classpathclass = "workbook\wbincdoku\dokuaction\Dokuaction{$act}";
                $method = "Event_{$eventname}_{$inType}Exec";
                if (class_exists($classpathclass)) {
                    if (method_exists($classpathclass, $method)) {
                        try {
                            // msg(microtime(true) . " " . strtoupper(print_r($ACT, true)) . " $inType $eventname ", '1');
                            $classpathclass::$method($Event, $inPara);
                        } catch (Throwable $t) {
                            doku\DokuAreaMsg::ThrowableAdd('Warning', $t);
                        }
                    }
                }
            }
        }
    }
    /* -------------------------------------------------------------------- */
    public function handle_event_after(Doku_Event $Event, $inPara) {
        $this->_Exec('After', $Event, $inPara);
    }
    /* -------------------------------------------------------------------- */
    public function handle_event_before(Doku_Event $Event, $inPara) {
        $this->_Exec('Before', $Event, $inPara);
    }
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
}