<?php

const APP_RUN = true;

const APP_BASE_PATH = __DIR__;
const APP_SYSTEM_PATH = __DIR__.'/system';
const APP_STORAGE_PATH = __DIR__.'/storage';
const APP_CACHE_PATH = APP_STORAGE_PATH.'/cache';

require APP_SYSTEM_PATH. '/app.php';
