<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/13
 * Time: 20:37
 */

namespace support;


class ErrorException extends \ErrorException
{
    /**
     * ErrorException constructor.
     * @param string $message
     * @param int $code
     * @param int $severity
     * @param string $filename
     * @param int $lineNo
     * @param \Exception|null $previous
     */
    public function __construct($message, $code = 0, $severity = 0, $filename, $lineNo, \Exception $previous =null)
    {
        parent::__construct($message, $code, $severity, $filename, $lineNo, $previous);
    }
}