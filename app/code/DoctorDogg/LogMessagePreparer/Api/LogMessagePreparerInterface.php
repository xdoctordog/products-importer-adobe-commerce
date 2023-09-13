<?php

namespace DoctorDogg\LogMessagePreparer\Api;

/**
 * Interface that allows to create the error message based on an exception.
 */
interface LogMessagePreparerInterface
{
    /**
     * Prepare and return error message.
     *
     * @param \Throwable $throwable
     * @return string
     */
    public function getErrorMessage(\Throwable $throwable): string;
}
