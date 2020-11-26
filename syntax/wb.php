<?php
use workbook\wbinc\doku;
use workbook\wbinc\dokucore;
use workbookcore\wbinc\util;
class syntax_plugin_workbook_wb extends DokuWiki_Syntax_Plugin {
    /* -------------------------------------------------------------------- */
    private $__Plugin = 'workbook';
    private $__Pattern = 'wb';
    /* -------------------------------------------------------------------- */
    public function getType() {
        return 'container';
    }
    /* -------------------------------------------------------------------- */
    function getPType() {
        return 'stack'; // complex
    }
    /* -------------------------------------------------------------------- */
    public function getSort() {
        return 133; // plugin_struct_output 155
    }
    /* -------------------------------------------------------------------- */
    public function connectTo($inMode) {
        if (strpos(doku\DokuSysGlobal::NsidGet(), 'zsync:sync:') !== false) return; // for zsync
        $this->Lexer->addSpecialPattern('<' . $this->__Pattern . '\/>', $inMode, 'plugin_' . $this->__Plugin . '_' . $this->__Pattern);
        $this->Lexer->addSpecialPattern('<' . $this->__Pattern . ' [^>]*?\/>', $inMode, 'plugin_' . $this->__Plugin . '_' . $this->__Pattern);
        $this->Lexer->addSpecialPattern('<' . $this->__Pattern . ' [^>]*?>.*?<\/' . $this->__Pattern . '>', $inMode, 'plugin_' . $this->__Plugin . '_' . $this->__Pattern);
    }
    /* -------------------------------------------------------------------- */
    public function handle($inMatch, $inState, $inPos, Doku_Handler $H) {
        return util\UtilSyntax::HandleAr($inMatch);
    }
    /* -------------------------------------------------------------------- */
    public function render($inMode, Doku_Renderer $R, $inData) {
        dokucore\DokucoreUtilSyntax::Render($inMode, $R, $inData);
    }
    /* -------------------------------------------------------------------- */
}