<?php

declare(strict_types=1);

namespace DoctorDogg\StopWatch\Api;

/**
 * The interface which allows you to store the date time in milliseconds and then get delta of some process.
 */
interface StopWatchInterface
{
    /**
     * Start (save the start date time checkpoint)
     *
     * @return void
     */
    public function start(): void;

    /**
     * Stop (save the stop date time checkpoint)
     *
     * @return void
     */
    public function stop(): void;

    /**
     * Get the delta between the start date time checkpoint and the stop date time checkpoint.
     *
     * @return string
     */
    public function delta(): string;

    /**
     * Clear saved values.
     *
     * @return bool
     */
    public function clear(): bool;
}
