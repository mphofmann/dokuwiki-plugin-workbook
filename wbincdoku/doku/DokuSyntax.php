<?php
namespace workbook\wbincdoku\doku;
use Doku_Renderer;
use workbook\wbinc\util;
use workbookcore\wbdef\wb;
class DokuSyntax {
    /* -------------------------------------------------------------------- */
    public static function RenderExec($inMode, Doku_Renderer $R, $inData): void {
        $inData['mode'] = $inMode;
        switch ($inMode) {
            case 'metadata':
                if (\_Wb_::ClassExists('mod\ModNsid')) {
                    $R->meta['title'] = \_Wb_::CmdExec("mod\ModNsid::HeadingGet nsid=" . DokuGlobal::NsidGet());
                }
                break;
            case 'workbook_csv':
            case 'xhtml':
                if ($inMode == 'workbook_csv' and @$_REQUEST['hash'] != @$inData['hash']) return;
                $R->nocache();
                $R->doc .= self::__WidgetGet($inData);
                break;
            default:
                DokuAreaMsg::Add('Warning', $inMode, '', 'Mode not defined.');
                break;
        }
    }
    /* -------------------------------------------------------------------- */
    public static function HandleAr($inMatch): array {
        $returns = ['match' => '', 'pattern' => '', 'class' => '', 'method' => '', 'mode' => '', 'attrs' => [], 'block' => ''];
        $returns['match'] = $inMatch;
        if (\_Wb_::ClassExists('util\UtilSyntax')) {
            $returns = util\UtilSyntax::HandleAr($inMatch);
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    private static function __WidgetGet($inData): string {
        $return = '';
        if (\_Wb_::ClassExists('wb\Wb')) {
            $return .= wb\Wb::WidgetGet($inData);
        } else {
            $return .= htmlspecialchars($inData['match']);
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
}