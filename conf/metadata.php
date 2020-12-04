<?php
$meta['connect_url']                = ['string'];
$meta['connect_username']           = ['string'];
$meta['connect_password']           = ['password'];
$meta['connect_version']            = ['multichoice', '_choices' => ['2020-07-29', '2018-04-22']];
$meta['connect_refresh_days']       = ['multichoice', '_choices' => ['7 days', '14 days', '30 days']];

$meta['lang1']                      = ['multichoice', '_choices' => ['', 'de', 'en', 'es', 'fr', 'it', 'pt']];
$meta['lang2']                      = ['multichoice', '_choices' => ['', 'de', 'en', 'es', 'fr', 'it', 'pt']];
$meta['lang3']                      = ['multichoice', '_choices' => ['', 'de', 'en', 'es', 'fr', 'it', 'pt']];
$meta['lang4']                      = ['multichoice', '_choices' => ['', 'de', 'en', 'es', 'fr', 'it', 'pt']];

$meta['company_name']               = ['string'];
$meta['company_mail']               = ['email'];
$meta['company_phone']              = ['string'];
$meta['company_home_url']           = ['string'];
$meta['company_privacy_url']        = ['string'];
$meta['company_impressum_url']      = ['string'];

$meta['acl_groups']                 = ['string'];

$meta['viewlist']                   = ['string'];
$meta['view_currency']              = ['string', '_pattern' => '/[A-Z]{3}/'];

$meta['view_cookielaw_text']        = [''];
$meta['view_mot']                   = [''];
$meta['view_mot_distprod']          = [''];
$meta['view_mot_distbeta']          = [''];
$meta['view_mot_distalpha']         = [''];
$meta['view_mot_distplan']          = [''];

$meta['head_style']                 = [''];