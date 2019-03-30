#!/usr/bin/env php
<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use corbomite\di\Di;

require __DIR__ . '/vendor/autoload.php';

var_dump(Di::get(\corbomite\configcollector\Collector::class));
die;
