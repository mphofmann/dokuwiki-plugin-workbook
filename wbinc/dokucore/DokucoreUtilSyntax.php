<?php
namespace workbook\wbinc\dokucore;
use Doku_Renderer;
use workbook\wbinc\doku;
use workbookcore\wbinc\mod;
use workbookcore\wbdef\wb;
class DokucoreUtilSyntax {
    /* -------------------------------------------------------------------- */
    public static function Render($inMode, Doku_Renderer $R, $inData) {
        $inData['mode'] = $inMode;
        switch ($inMode) {
            case 'metadata':
                $R->meta['title'] = mod\ModNsid::HeadingGet(doku\DokuGlobal::NsidGet());
                break;
            case 'workbook_csv':
            case 'xhtml':
                if ($inMode == 'workbook_csv' and @$_REQUEST['hash'] != @$inData['hash']) return true;
                $R->nocache();
                $R->doc .= wb\Wb::WidgetExec($inData);
                break;
            default:
                doku\DokuXhtmlMsg::Add('Warning', $inMode, '', 'Mode not defined.');
                break;
        }
        return true;
    }
    /* -------------------------------------------------------------------- */
}