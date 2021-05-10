<?php
namespace workbook\wbincdoku\doku;
use Doku_Handler;
use Doku_Renderer_xhtml;
use DokuWiki_Syntax_Plugin;
use function plugin_isdisabled;
use function plugin_load;
class DokuPlugin {
    /* -------------------------------------------------------------------- */
    public static function DisabledIs($inId): bool {
        return plugin_isdisabled($inId);
    }
    /* -------------------------------------------------------------------- */
    public static function RenderGet($inType, $inId, $inMarkup): string {
        if (($ps = self::LoadObj($inType, $inId)) === false) return false;
        return self::__RenderGet($ps, $inMarkup);
    }
    /* -------------------------------------------------------------------- */
    public static function LoadObj($inType, $inId): object {
        $pos = strpos($inId, '_');
        $id = ($pos === false) ? $inId : substr($inId, 0, $pos);
        if (plugin_isdisabled($id)) return (object)null;
        return plugin_load($inType, $inId);
    }
    /* -------------------------------------------------------------------- */
    private static function __RenderGet(DokuWiki_Syntax_Plugin $Plugin, $inMarkup): string {
        $dhandler = new Doku_Handler();
        $data = $Plugin->handle($inMarkup, null, null, $dhandler);
        $r = new Doku_Renderer_xhtml();
        $Plugin->render('xhtml', $r, $data);
        return $r->doc;
    }
    /* -------------------------------------------------------------------- */
}