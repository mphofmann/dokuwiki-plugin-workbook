<?php
use workbook\wbinc\admin;
use workbook\wbinc\doku;
use workbookcore\wbinc\env;
use workbookcore\wbinc\mod;
use workbookcore\wbinc\xhtml;
class admin_plugin_workbook_marketplace extends DokuWiki_Admin_Plugin {
    /* -------------------------------------------------------------------- */
    private $__Out = '';
    private $__Cmds = ['cmd' => ''];
    /* -------------------------------------------------------------------- */
    public function forAdminOnly() {
        return false;
    }
    /* -------------------------------------------------------------------- */
    public function getMenuIcon() {
        return DOKU_PLUGIN . 'workbook/admin_marketplace.svg';
    }
    /* -------------------------------------------------------------------- */
    public function getMenuSort() {
        return 7771;
    }
    /* -------------------------------------------------------------------- */
    public function getMenuText($language) {
        return $this->getLang('menu_marketplace');
    }
    /* -------------------------------------------------------------------- */
    function handle() {
        $str = admin\AdminExec::HandleExec($this->__Cmds);
        if ($str === false) return;
        $this->__Out = $str;
    }
    /* -------------------------------------------------------------------- */
    function html() {
        // Heading
        echo('<h1>Workbook</h1>');
        echo(admin\AdminXhtml::MenuGet());
        admin\AdminExec::OutputEcho($this->__Out);
        if (strpos(@constant('WB_RUNMODE'), 'workbookcore-ok') === false) {
            echo admin\AdminXhtml::TextInstallLinkGet();
        } else {
            echo('<h2>Marketplace</h2>');
            // Form
            echo('<form action="' . doku\DokuUtil::WikiLinkGet(doku\DokuSysGlobal::NsidGet()) . '" method="post">');
            echo('  <input type="hidden" name="do"   value="admin" />');
            echo('  <input type="hidden" name="page" value="' . $this->getPluginName() . '_marketplace" />');
            doku\DokuXhtmlForm::SecTokenEcho();
            // Table
            $rows = $this->__ArrayMarketplaceGet();
            if (empty($rows)) {
                echo "---";
            } else {
                $styles = ['height:25px; width:150px; white-space:nowrap;', 'min-width:200px;', 'min-width:200px;', 'width:100px;text-align:center;', 'width:100px;text-align:center;', 'width:100px;text-align:center;', 'width:100px;text-align:center;'];
                echo admin\AdminXhtml::TableGet($rows, $styles);
            }
            // Form
            echo('</form>');
        }
    }
    /* -------------------------------------------------------------------- */
    private function __ArrayMarketplaceGet() {
        $returns = [];
        $ar = mod\ModWb::IniArs();
        if (!empty($ar)) {
            $returns[] = ['TH:WORKBOOK', 'TH:Tagline', 'TH:Tools', 'TH:Dist', 'TH:Status', 'TH:Manage'];
            $rar = ['prod' => [], 'beta' => [], 'alpha' => [], 'plan' => [],];
            foreach ($ar as $wb => $ar2) {
                $color = 'white';
                if (is_dir("data/pages/$wb")) {
                    $color = (scandir("data/pages/$wb") >= 2) ? 'green' : $color;
                }
                $topic = "<b><i class='fa {$ar2['*']['icon']}' ></i> " . strtoupper($wb) . "</b>";
                if ($color == 'green') {
                    $topic = "<a href='?id=$wb:start'>$topic</a>";
                }
                $tagline = $ar2['*']['tagline'];
                $tools = '';
                if (is_array($ar2['tools'])) {
                    foreach ($ar2['tools'] as $id => $val) {
                        if ($val == '1') $tools .= "{$id} ";
                    }
                }
                $dist = (!empty($ar2['*']['dist']) and @$ar2['*']['dist'] != 'prod') ? strtr("[{$ar2['*']['dist']}]", env\EnvConf::EntitiesAr()) : '';
                $status = xhtml\XhtmlUnicode::StatusGet($color);
                if ($color == 'green') {
                    $button = admin\AdminXhtml::LinkGet('?do=admin&page=workbook_workbook');
                } else {
                    $attr = (mod\ModWb::CommandEnabledCheck($wb, 'install')) ? '' : 'disabled';
                    $button = admin\AdminXhtml::ButtonGet("admincore\AdmincoreWb::Install wb=$wb", 'install', $attr, 'small');
                }
                $rar[@$ar2['*']['dist']][] = ["&nbsp;&nbsp;&nbsp;" . $topic, $tagline, $tools, $dist, $status, $button];
            }
            foreach (['prod', 'beta', 'alpha', 'plan', 'system'] as $val) {
                if (!empty($rar[$val])) {
                    $returns[] = ['TH:' . ucfirst($val)];
                    $returns = array_merge($returns, $rar[$val]);
                }
                unset($rar[$val]);
            }
            if (!empty($rar)) {
                $returns[] = ['TH:Other'];
                foreach ($rar as $id => $ar) {
                    $returns = array_merge($returns, $rar[$id]);
                }
            }
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
}