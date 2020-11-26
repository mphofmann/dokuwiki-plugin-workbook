<?php
namespace workbook\wbinc\doku;
class DokuPlugin {
    /* -------------------------------------------------------------------- */
    public static function FeedbackGet($inData = []) { // div added by plugin
        if (($ps = DokuXhtmlPlugin::Load('syntax', 'feedback')) === false) return;
        return DokuXhtmlPlugin::Render($ps, '{{FEEDBACK}}');
    }
    /* -------------------------------------------------------------------- */
    public static function YearboxGet($inData = []) { // div added by plugin
        if (($ps = DokuXhtmlPlugin::Load('syntax', 'yearbox')) === false) return false;
        $year = (empty($inData['attrs']['date-year'])) ? date('Y') : $inData['attrs']['date-year'];
        $ns = DokuSysGlobal::NsGet();
        // TODO ns=.: not working with title
        $markup = "{{yearbox>ns=$ns;year=$year;align=left;name=;size=0}}";
        return DokuXhtmlPlugin::Render($ps, $markup);
    }
    /* -------------------------------------------------------------------- */
}