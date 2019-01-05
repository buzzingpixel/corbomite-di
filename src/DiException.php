<?php
declare(strict_types=1);

namespace corbomite\di;

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use Exception;
use Throwable;

class DiException extends Exception
{
    public function __construct(
        string $message = '',
        int $code = 500,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
