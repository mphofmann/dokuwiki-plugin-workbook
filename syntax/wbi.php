<?php
use workbook\wbinc\doku;
use workbook\wbinc\dokucore;
use workbookcore\wbinc\util;
class syntax_plugin_workbook_wbi extends DokuWiki_Syntax_Plugin {
    /* -------------------------------------------------------------------- */
    private $__Plugin = 'workbook';
    private $__Pattern = 'wbi';
    /* -------------------------------------------------------------------- */
    public function getType() {
        return 'substition';
    }
    /* -------------------------------------------------------------------- */
    function getPType() {
        return 'normal'; // inline
    }
    /* -------------------------------------------------------------------- */
    public function getSort() {
        return 131;
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
        return util\UtilSyntax::HandleAr($inMatch);
    }
    /* -------------------------------------------------------------------- */
    public function render($inMode, Doku_Renderer $R, $inData) {
        dokucore\DokucoreUtilSyntax::Render($inMode, $R, $inData);
    }
    /* -------------------------------------------------------------------- */
}