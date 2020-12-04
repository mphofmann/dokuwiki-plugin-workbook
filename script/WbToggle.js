/* -------------------------------------------------------------------- */
// ClickFullscreen
jQuery('button.wbc_clickfullscreen').click(function () {
    var $button = jQuery(this);
    var $div = $button.parent();

    $div.toggleClass('wbc_clickfullscreen_show');
    var $wbcontent = jQuery('#wb__content');
    $wbcontent.css('z-index', 'initial');
    if ($div.hasClass('wbc_clickfullscreen_show')) {
        $button.html("<i class='fas fa-compress'></i>");
    } else {
        $button.html("<i class='fas fa-expand'></i>");
    }
});
/* -------------------------------------------------------------------- */
// ToggleShow
jQuery('#wb__site').find('button.wbc_toggleshow').each(function () {
    var $button = jQuery(this);
    var $div = $button.parent();
    if (WbCookie.getValue($div.data('wbdivid') + '_toggleshow') !== '1') {
        $div.toggleClass('wbc_toggleshow_closed');
    }
});
jQuery('button.wbc_toggleshow').click(function () {
    var $button = jQuery(this);
    var $div = $button.parent();
    $div.toggleClass('wbc_toggleshow_closed');
    if ($div.hasClass('wbc_toggleshow_closed')) {
        WbCookie.setValue($div.data('wbdivid') + '_toggleshow', '1');
    } else {
        WbCookie.setValue($div.data('wbdivid') + '_toggleshow', '');
    }
});
/* -------------------------------------------------------------------- */