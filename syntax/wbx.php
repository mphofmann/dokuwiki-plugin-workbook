<?php
use workbookcore\wbinc\util;
use workbookcore\wbinc\xhtml;
class syntax_plugin_workbook_wbx extends DokuWiki_Syntax_Plugin {
    /* -------------------------------------------------------------------- */
    private $__Plugin = 'workbook';
    private $__Pattern = 'wbx';
    /* -------------------------------------------------------------------- */
    public function getType() {
        return 'formatting';
    }
    /* -------------------------------------------------------------------- */
    function getPType() {
        return 'normal'; // inline
    }
    /* -------------------------------------------------------------------- */
    public function getSort() {
        return 133; // plugin_struct_output 155
    }
    /* -------------------------------------------------------------------- */
    public function connectTo($inMode) {
        $this->Lexer->addSpecialPattern('\[\[[^/@\\\\>]*?\]\]', $inMode, 'plugin_' . $this->__Plugin . '_' . $this->__Pattern); // only internal links
        // $this->Lexer->addSpecialPattern('\[\[.*?\]\]', $inMode, 'plugin_' . $this->__Plugin . '_' . $this->__Pattern); // all links
    }
    /* -------------------------------------------------------------------- */
    public function handle($inMatch, $inState, $inPos, Doku_Handler $H) {
        return util\UtilSyntax::HandleAr($inMatch, false);
    }
    /* -------------------------------------------------------------------- */
    public function render($inMode, Doku_Renderer $R, $inData) {
        $R->doc .= xhtml\XhtmlLink::StringRender($inData['match']);
    }
    /* -------------------------------------------------------------------- */
}