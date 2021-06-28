<?php
$meta['connect_url']                = ['string'];
$meta['connect_username']           = ['string'];
$meta['connect_password']           = ['password'];
$meta['connect_mail']               = ['email'];
$meta['connect_terms']              = ['multichoice', '_choices' => ['accepted']];
$meta['connect_dist']               = ['multichoice', '_choices' => ['stable', 'testing', 'unstable']];
$meta['connect_version']            = ['multichoice', '_choices' => ['stable', 'testing', 'unstable']];
$meta['connect_refresh_days']       = ['multichoice', '_choices' => ['7 days', '14 days', '30 days']];

$meta = array_merge($meta, \_Wb_::WbconfAr('meta'));