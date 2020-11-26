<?php
include_once (((substr(getcwd(), -strlen('/lib/exe')) == '/lib/exe') ? '../../' : '') . 'lib/plugins/workbook/__dokuinit.php');
class action_plugin_workbook_action extends workbook\action\a_Action {
    /* -------------------------------------------------------------------- */
    protected $_Events = [ //
        'all' => [ //
            'ACTION_ACT_PREPROCESS' => 'workbook\action\inccore\ActionAll',             // $ACT
            'TPL_METAHEADER_OUTPUT' => 'workbook\action\inccore\ActionAll',             // font-awesome stylesheet
            'AUTH_ACL_CHECK' => 'workbookuser\action\inccore\ActionAll',                // ACL-WB logic
        ], //
        'admin' => [ //
            'PLUGIN_CONFIG_PLUGINLIST' => 'workbook\action\inccore\ActionAdmin',        //
        ], //
        'ajax' => [ //
            'TOOLBAR_DEFINE' => 'workbook\action\inccore\ActionAjax',                   //
            'AJAX_CALL_UNKNOWN' => 'workbook\action\inccore\ActionAjax',                // tag Do
            'PLUGIN_MOVE_PAGE_RENAME' => 'workbook\action\inccore\ActionAjax',          // move rename
        ], //
        'show' => [ //
            'DOKUWIKI_STARTED' => 'workbook\action\inccore\ActionShow',                 // forward start to e.g. start_de
            'TPL_CONTENT_DISPLAY' => 'workbook\action\inccore\ActionShow',              // output modifications
            'RENDERER_CONTENT_POSTPROCESS' => 'workbook\action\inccore\ActionShow',     //
            'DOKUWIKI_DONE' => 'workbook\action\inccore\ActionShow',                    //
        ], //
        'edit' => [ //
            'COMMON_PAGETPL_LOAD' => 'workbook\action\inccore\ActionEdit',              // new page template
        ], //
        'save' => [ //
            'COMMON_WIKIPAGE_SAVE' => 'workbook\action\inccore\ActionSave',             // update actions
            'INDEXER_PAGE_ADD' => 'workbook\action\inccore\ActionSave',                 // indexer actions
        ], //
    ];
    /* -------------------------------------------------------------------- */
}