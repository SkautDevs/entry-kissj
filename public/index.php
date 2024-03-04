<?php

declare(strict_types=1);

use EntryKissj\Application\ApplicationGetter;

require __DIR__ . '/../vendor/autoload.php';

session_start();

(new ApplicationGetter())->getApp()->run();
