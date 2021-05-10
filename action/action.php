<?php
include_once(((substr(getcwd(), -strlen('/lib/exe')) == '/lib/exe') ? '../../' : '') . 'lib/plugins/workbook/_dokuinit_.php');
class action_plugin_workbook_action extends workbook\wbdefdoku\dokuaction\a_Dokuaction {
    /* -------------------------------------------------------------------- */
    protected $_Events = [ //
        'JobStart' => [ //
            'AUTH_LOGIN_CHECK' => '',           // Auth login check
            'AUTH_ACL_CHECK' => '',             // Auth ACL-WB logic
            'DOKUWIKI_STARTED' => '',           // Start (forward start to e.g. start_de)
            'ACTION_ACT_PREPROCESS' => '',      // $ACT
            'TPL_METAHEADER_OUTPUT' => '',      // Styles (font-awesome stylesheet)
        ], //
        'Admin' => [ //
            'PLUGIN_CONFIG_PLUGINLIST' => '',   //
        ], //
        'Ajax' => [ //
            'TOOLBAR_DEFINE' => '',             // toolbar
            'PLUGIN_MOVE_PAGE_RENAME' => '',    // move rename
        ], //
        'Edit' => [ //
            'COMMON_PAGETPL_LOAD' => '',        // new page template
        ], //
        'Save' => [ //
            'COMMON_WIKIPAGE_SAVE' => '',       // update actions
            'INDEXER_PAGE_ADD' => '',           // indexer actions
        ], //
        'JobEnd' => [ //
            'TPL_CONTENT_DISPLAY' => '',        // Content (adjustments)
            'DOKUWIKI_DONE' => '',              // End (debug)
        ], //
    ];
    /* -------------------------------------------------------------------- */
}