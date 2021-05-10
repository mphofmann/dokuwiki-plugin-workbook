<?php
use workbook\wbincdoku\doku;
class renderer_plugin_workbook_csv extends Doku_Renderer {
    /* -------------------------------------------------------------------- */
    public function __construct() {
        $filepath = (((substr(getcwd(), -strlen('/lib/exe')) == '/lib/exe') ? '../../' : '') . 'workbook/lib/plugins/workbook/_dokuinit_.php');
        if (file_exists($filepath)) include_once($filepath);
    }
    /* -------------------------------------------------------------------- */
    function getFormat() {
        return 'workbook_csv';
    }
    /* -------------------------------------------------------------------- */
    function document_start() {
        $filename = noNS(doku\DokuGlobal::NsidGet()).'.csv';
        $headers = array('Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="' . $filename . '";');
        p_set_metadata(doku\DokuGlobal::NsidGet(), array('format' => array('workbook_csv' => $headers)));
        // no cache
        $this->nocache();
    }
    /* -------------------------------------------------------------------- */
}