<?php
namespace workbook\wbinc\dokucore;
use Doku_Form;
use workbook\wbinc\doku;
use workbookcore\wbinc\base;
use workbookcore\wbinc\env;
use workbookcore\wbinc\sys;
use workbookcore\wbinc\util;
use workbookcore\wbinc\xhtml;
use function html_login;
use function lock;
use function media_delete;
use function media_inuse;
use function media_save;
use function saveWikiText;
use function send_redirect;
use function unlock;
class DokucoreXhtmlForm {
    /* -------------------------------------------------------------------- */
    public static function Search($inData = []) {
        $return = '';
        $cssclass = util\UtilCss::ClassesGet(__METHOD__, 'method');
        $ar = [];
        $ar['action'] = doku\DokuUtil::WikiLinkGet(base\BaseGlobal::NsidGet());
        $ar['method'] = 'get';
        $ar['id'] = $cssclass;
        $ar['class'] = "$cssclass search-results-form";
        global $INPUT;
        $form = new Doku_Form($ar);
        $form->addHidden('do', 'search');
        $form->addHidden('id', doku\DokuGlobal::NsidGet());
        $form->addHidden('sf', '1');
        if ($INPUT->has('min')) $form->addHidden('min', $INPUT->str('min'));
        if ($INPUT->has('max')) $form->addHidden('max', $INPUT->str('max'));
        if ($INPUT->has('str')) $form->addHidden('str', $INPUT->str('str'));
        $form->addElement(form_makeField('text', 'q', '', '', '', 'searchtext'));
        $form->addElement(form_makeButton('submit', '', xhtml\XhtmlIcon::Get('fa-search'), ['class' => 'buttonsubmit']));
        $return .= $form->getForm();
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function Login($inData = []) {
        $return = '';
        if (env\EnvUserCurrent::Get() == '') {
            ob_start();
            html_login();
            $return .= ob_get_clean();
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function NsAdd() {
        if (doku\DokuAcl::NsAclGet(base\Base::NsGet()) < base\BaseGlobal::ConstGet('AUTH_DELETE')) return '';
        $return = '<div class="' . util\UtilCss::ClassesGet(__METHOD__, 'class method') . '">';
        $form = new Doku_Form(self::__FormAr(__METHOD__));
        $form->addHidden('data-actionform', 'dokucore\DokucoreXhtmlForm::NsAddAction');
        $form->addElement(form_makeField('text', 'input', '', ''));
        $form->addElement(form_makeButton('submit', '', xhtml\XhtmlIcon::Get('icon submit-add'), ['class' => 'wbc_guibuttom_submit_mini']));
        $return .= $form->getForm();
        $return .= '</div>';
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function NsAddAction() {
        if (!doku\DokuXhtmlForm::SecTokenCheck()) return false;
        if (!empty($_REQUEST['input'])) {
            $input = doku\DokuUtil::IdCleanGet($_REQUEST['input']);
            $ns = base\Base::NsGet() . ":$input";
            $nspath = 'data/pages/' . strtr($ns, [':' => '/']) . '/';
            if (!file_exists($nspath)) {
                util\UtilPath::MkdirCheck($nspath);
            }
            // $filepath = "{$nspath}start.txt";
            // if (!file_exists($filepath)) {
            //     file_put_contents($filepath, '<wb/>');
            // }
            $url = doku\DokuUtil::WikiLinkGet("{$ns}:start");
            send_redirect($url);
        }
        return false;
    }
    /* -------------------------------------------------------------------- */
    public static function PageAdd() {
        if (doku\DokuAcl::NsAclGet(base\Base::NsGet()) < base\BaseGlobal::ConstGet('AUTH_CREATE')) return '';
        $return = '<div class="' . util\UtilCss::ClassesGet(__METHOD__, 'class method') . '">';
        // langs
        $langs = explode('-', env\EnvLang::Get());
        $langs = array_unique($langs);
        $langs = array_filter($langs);
        $langselected = env\EnvView::LangGet('1');
        // template
        $tpls = [];
        $tpls[] = ''; // blank for simple heading
        foreach (sys\SysNs::ScandirAr("zsync:sync:" . sys\SysRemote::VersionGet() . ':template', '', 'pages', 'files') as $file) {
            if (substr($file, 0, strlen('template.')) != 'template.') continue;
            $tpls[$file] = $file;
        }
        // form
        $form = new Doku_Form(self::__FormAr(__METHOD__));
        $form->addHidden('data-actionform', 'dokucore\DokucoreXhtmlForm::PageAddAction');
        $form->addElement(form_makeField('text', 'input', '', ''));
        if (!empty($langs)) {
            array_unshift($langs, '');
            $form->addElement(form_makeListboxField('lang', $langs, $langselected, '', '', '', ['style' => 'max-width:3.5em; padding-left:0; padding-right:0;']));
        }
        if (!empty($tpls)) {
            $form->addElement(form_makeListboxField('template', $tpls, '', '', '', '', ['style' => ''])); // max-width:2em
        }
        $form->addElement(form_makeButton('submit', '', xhtml\XhtmlIcon::Get('icon submit-add'), ['class' => 'wbc_guibuttom_submit_mini']));
        $return .= $form->getForm();
        $return .= '</div>';
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function PageAddAction() {
        if (!doku\DokuXhtmlForm::SecTokenCheck()) return false;
        if (!empty($_REQUEST['input'])) {
            $input = doku\DokuUtil::IdCleanGet($_REQUEST['input']);
            $id = base\Base::NsGet() . ":$input";
            $id .= (empty($_REQUEST['lang'])) ? '' : "_{$_REQUEST['lang']}";
            $url = doku\DokuUtil::WikiLinkGet($id) . "&do=edit" . "&template={$_REQUEST['template']}&input={$_REQUEST['input']}";
            send_redirect($url);
        }
        return true;
    }
    /* -------------------------------------------------------------------- */
    public static function PageStartAdd($inLang = '') {
        if (empty($inLang)) return '';
        if (doku\DokuAcl::NsAclGet(base\Base::NsGet()) < base\BaseGlobal::ConstGet('AUTH_CREATE')) return '';
        $return = '<div class="' . util\UtilCss::ClassesGet(__METHOD__, 'class method') . '">';
        $form = new Doku_Form(self::__FormAr(__METHOD__));
        $form->addHidden('data-actionform', 'dokucore\DokucoreXhtmlForm::PageStartAddAction');
        $form->addHidden('lang', $inLang);
        $form->addElement(form_makeButton('submit', '', $inLang, ['class' => 'wbc_guibuttom_submit_mini', 'title' => 'start_en']));
        $return .= $form->getForm();
        $return .= '</div>';
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function PageStartAddAction() {
        if (!doku\DokuXhtmlForm::SecTokenCheck()) return false;
        if (!empty($_REQUEST['lang'])) {
            $_strlang = ($_REQUEST['lang'] == '**') ? '' : '_' . doku\DokuUtil::IdCleanGet($_REQUEST['lang']);
            $id = base\Base::NsGet() . ":start{$_strlang}";
            $url = doku\DokuUtil::WikiLinkGet($id) . "&do=edit";
            send_redirect($url);
        }
        return true;
    }
    /* -------------------------------------------------------------------- */
    public static function PageDelete($inNsid = '') {
        if (empty($inNsid)) return false;
        $auth = doku\DokuAcl::NsAclGet(base\Base::NsGet());
        if ($auth < base\BaseGlobal::ConstGet('AUTH_DELETE')) return '';
        $return = '<div class="' . util\UtilCss::ClassesGet(__METHOD__, 'class method') . '">';
        $form = new Doku_Form(self::__FormAr(__METHOD__));
        $form->addHidden('data-actionform', 'dokucore\DokucoreXhtmlForm::PageDeleteAction');
        $form->addHidden('nsid', $inNsid);
        $form->addElement(form_makeButton('submit', '', xhtml\XhtmlIcon::Get('icon submit-delete'), ['onclick' => "return confirm('Delete?')", 'class' => 'wbc_guibuttom_submit_mini', 'title' => $inNsid]));
        $return .= $form->getForm();
        $return .= '</div>';
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function PageDeleteAction() {
        if (!doku\DokuXhtmlForm::SecTokenCheck()) return false;
        $nsid = @$_REQUEST['nsid'];
        if (empty($nsid)) return false;
        $nsidfile = 'data/pages/' . strtr($nsid, [':' => '/']) . '.txt';
        if (file_exists($nsidfile)) {
            lock($nsid);
            saveWikiText($nsid, '', '', true);
            unlock($nsid);
            base\BaseXhtmlMsg::Add('Success', '', $nsid, 'Page deleted.');
        }
        return true;
    }
    /* -------------------------------------------------------------------- */
    public static function MediaUpload() { // multiple
        $auth = doku\DokuAcl::NsAclGet(base\Base::NsGet());
        if ($auth < base\BaseGlobal::ConstGet('AUTH_UPLOAD')) return '';
        $return = '';
        $cssclass = util\UtilCss::ClassesGet(__METHOD__, 'method');
        // styles see inctag/doku
        $return .= "<div class='$cssclass'>";
        $form = new Doku_Form(self::__FormAr(__METHOD__, ['enctype' => 'multipart/form-data']));
        $form->addElement(doku\DokuXhtmlForm::SecTokenEcho());
        $form->addHidden('ow', true); // owerwrite
        $form->addHidden('data-actionform', 'dokucore\DokucoreXhtmlForm::MediaUploadAction');
        $form->addElement(form_makeFileField('upload[]', xhtml\XhtmlIcon::Get('icon submit-upload'), "{$cssclass}_file", '', ['onchange' => 'form.submit()', 'class' => 'wbc_guibuttom_submit_mini', 'multiple' => 'multiple']));
        $return .= $form->getForm();
        $return .= "</div>";
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function MediaUploadAction() { // multiple
        if (!doku\DokuXhtmlForm::SecTokenCheck()) return false;
        $return = false;
        $files = self::__FilesReAr($_FILES['upload']);
        foreach ($files as $ar) {
            if ($ar['error'] == '1') {
                base\BaseXhtmlMsg::Add('Notice', '', $ar['name'], 'Media upload error. [Size?]');
                $return = false;
                continue;
            }
            if ($ar['tmp_name']) {
                $ns = base\Base::NsGet();
                $res = media_save(['name' => $ar['tmp_name'],], "$ns:{$ar['name']}", true, doku\DokuAcl::NsAclGet($ns), 'copy_uploaded_file');
                if ($res === false) {
                    base\BaseXhtmlMsg::Add('Notice', '', $ar['name'], 'File save failed.');
                    $return = false;
                }
            }
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function MediaDelete($inNsmedia = '') {
        if (empty($inNsmedia)) return false;
        if (media_inuse($inNsmedia)) return false;
        $auth = doku\DokuAcl::NsAclGet(base\Base::NsGet());
        if ($auth < base\BaseGlobal::ConstGet('AUTH_DELETE')) return '';
        $return = '<div class="' . util\UtilCss::ClassesGet(__METHOD__, 'class method') . '">';
        $form = new Doku_Form(self::__FormAr(__METHOD__));
        $form->addHidden('data-actionform', 'dokucore\DokucoreXhtmlForm::MediaDeleteAction');
        $form->addHidden('media', $inNsmedia);
        $form->addElement(form_makeButton('submit', '', xhtml\XhtmlIcon::Get('icon submit-delete'), ['onclick' => "return confirm('Delete?')", 'class' => 'wbc_guibuttom_submit_mini', 'title' => $inNsmedia]));
        $return .= $form->getForm();
        $return .= '</div>';
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function MediaDeleteAction() {
        if (!doku\DokuXhtmlForm::SecTokenCheck()) return false;
        $media = @$_REQUEST['media'];
        if (empty($media)) return false;
        $mediafile = 'data/media/' . strtr($media, [':' => '/']);
        if (file_exists($mediafile)) {
            $ns = base\Base::NsGet();
            media_delete($media, doku\DokuAcl::NsAclGet($ns));
            base\BaseXhtmlMsg::Add('Success', '', $media, 'Media deleted.');
        }
        return true;
    }
    /* -------------------------------------------------------------------- */
    private static function __FilesReAr($inFiles) {
        $returns = [];
        foreach ($inFiles as $id => $all) {
            foreach ($all as $i => $val) {
                $returns[$i][$id] = $val;
            }
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    private static function __FormAr($inMethod, $inAr = []) {
        $cssclass = util\UtilCss::ClassesGet($inMethod, 'method');
        $returns = $inAr;
        $returns['method'] = 'post';
        $returns['id'] = $cssclass . uniqid();
        $returns['class'] = $cssclass;
        $returns['action'] = '?id=' . base\BaseGlobal::NsidGet();
        $returns['data-actionform'] = doku\DokuUtil::WikiLinkGet(base\BaseGlobal::NsidGet());
        return $returns;
    }
    /* -------------------------------------------------------------------- */
}