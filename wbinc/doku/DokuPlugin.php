<?php
namespace workbook\wbinc\doku;
use Doku_Handler;
use Doku_Renderer_xhtml;
use DokuWiki_Syntax_Plugin;
use function plugin_isdisabled;
use function plugin_load;
class DokuPlugin {
    /* -------------------------------------------------------------------- */
    public static function DisabledIs($inId) {
        return plugin_isdisabled($inId);
    }
    /* -------------------------------------------------------------------- */
    public static function LoadRender($inType, $inId, $inMarkup) {
        if (($ps = self::Load($inType, $inId)) === false) return false;
        return self::Render($ps, $inMarkup);
    }
    /* -------------------------------------------------------------------- */
    public static function Load($inType, $inId) {
        $pos = strpos($inId, '_');
        $id = ($pos === false) ? $inId : substr($inId, 0, $pos);
        if (plugin_isdisabled($id)) return false;
        return plugin_load($inType, $inId);
    }
    /* -------------------------------------------------------------------- */
    public static function Render(DokuWiki_Syntax_Plugin $Plugin, $inMarkup) {
        $dhandler = new Doku_Handler();
        $data = $Plugin->handle($inMarkup, null, null, $dhandler);
        $r = new Doku_Renderer_xhtml();
        $Plugin->render('xhtml', $r, $data);
        return $r->doc;
    }
    /* -------------------------------------------------------------------- */
}