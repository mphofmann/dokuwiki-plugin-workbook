<?php
use workbook\wbincdoku\doku;
class syntax_plugin_workbook_wbb extends DokuWiki_Syntax_Plugin {
    /* -------------------------------------------------------------------- */
    private $__Plugin = 'workbook';
    private $__Pattern = 'wbb';
    /* -------------------------------------------------------------------- */
    public function getType() {
        return 'substition';
    }
    /* -------------------------------------------------------------------- */
    function getPType() {
        return 'block'; // paragraph
    }
    /* -------------------------------------------------------------------- */
    public function getSort() {
        return 132;
    }
    /* -------------------------------------------------------------------- */
    public function connectTo($inMode) {
        if (strpos(doku\DokuGlobal::NsidGet(), 'zsync:sync:') !== false) return; // for zsync
        $this->Lexer->addSpecialPattern('<' . $this->__Pattern . '\/>', $inMode, 'plugin_' . $this->__Plugin . '_' . $this->__Pattern);
        $this->Lexer->addSpecialPattern('<' . $this->__Pattern . ' [^>]*?\/>', $inMode, 'plugin_' . $this->__Plugin . '_' . $this->__Pattern);
        $this->Lexer->addSpecialPattern('<' . $this->__Pattern . ' [^>]*?>.*?<\/' . $this->__Pattern . '>', $inMode, 'plugin_' . $this->__Plugin . '_' . $this->__Pattern);
    }
    /* -------------------------------------------------------------------- */
    public function handle($inMatch, $inState, $inPos, Doku_Handler $H) {
        return doku\DokuSyntax::HandleAr($inMatch);
    }
    /* -------------------------------------------------------------------- */
    public function render($inMode, Doku_Renderer $R, $inData) {
        doku\DokuSyntax::RenderExec($inMode, $R, $inData);
    }
    /* -------------------------------------------------------------------- */
}