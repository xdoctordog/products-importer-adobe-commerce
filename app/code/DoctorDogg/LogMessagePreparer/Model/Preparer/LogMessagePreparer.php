<?php

declare(strict_types = 1);

namespace DoctorDogg\LogMessagePreparer\Model\Preparer;

use DoctorDogg\LogMessagePreparer\Api\LogMessagePreparerInterface;

/**
 * The class that allows you to generate an error message based on an exception object.
 */
class LogMessagePreparer implements LogMessagePreparerInterface
{
    /**
     * Get error message.
     *
     * @param \Throwable $throwable
     * @return string
     */
    public function getErrorMessage(\Throwable $throwable): string
    {
        return 'File: ' . $throwable->getFile() . ' '
        . 'on the line: ' . $throwable->getLine() . ' ' .
        $throwable->getMessage();
    }
}
