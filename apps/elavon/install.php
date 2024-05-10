<?php

$d = ORM::for_table('sys_pg')
    ->where('processor', 'elavon')
    ->find_one();

if (!$d) {
    $c = ORM::for_table('sys_pg')->create();
    $c->name = 'Elavon';
    $c->settings = 'Merchant ID';
    $c->value = 'Enter Merchant ID Here';
    $c->processor = 'elavon';
    $c->ins = '';
    $c->c1 = 'Enter User ID Here';
    $c->c2 = 'Enter PIN Here';
    $c->c3 = '';
    $c->c4 = '';
    $c->c5 = '';
    $c->status = 1;
    $c->sorder = 0;
    $c->logo = 'apps/elavon/views/imgs/elavon_logo.svg';
    $c->mode = 'Live';
    $c->save();
}

$pref = setSharedPreferences('elavon_webhook_secret', 0, 'key', _raid(16));
