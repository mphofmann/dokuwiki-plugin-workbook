<?php
use dokuwiki\plugin\bureaucracy\interfaces;
use workbook\wbincdoku\doku;
class helper_plugin_bureaucracy_handler_wbactionscripthandler implements interfaces\bureaucracy_handler_interface {
    /* -------------------------------------------------------------------- */
    public function handleData($fields, $thanks) {
        $filepath = '';
        foreach ($fields as $field) {
            if ($field->opt['label'] == 'actionscript') {
                $filepath = $field->opt['value'];
            }
        }
        if (file_exists($filepath)) {
            include($filepath);
            __WbActionScriptGet($fields, $thanks);
        } else {
            doku\DokuAreaMsg::Add('Warning', __METHOD__, '', 'Script not found.');
        }
    }
    /* -------------------------------------------------------------------- */
}