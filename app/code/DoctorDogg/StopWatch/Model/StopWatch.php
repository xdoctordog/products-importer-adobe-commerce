<?php

declare(strict_types=1);

namespace DoctorDogg\StopWatch\Model;

use \DoctorDogg\StopWatch\Api\StopWatchInterface;

/**
 * The class which allows you to store the date time in milliseconds and then get delta of some process.
 */
class StopWatch implements StopWatchInterface
{
    /**
     * @var int $startTimeStamp
     */
    private int $startTimeStamp = 0;

    /**
     * @var int $startMicroTimeStamp
     */
    private int $startMicroTimeStamp = 0;

    /**
     * @var int $endTimeStamp
     */
    private int $endTimeStamp = 0;

    /**
     * @var int $endMicroTimeStamp
     */
    private int $endMicroTimeStamp = 0;

    /**
     * Start (save the start date time checkpoint)
     *
     * @return void
     */
    public function start(): void
    {
        $startTime = \microtime();
        $startTimeParts = \explode(' ', $startTime);
        $this->startMicroTimeStamp = (int)(($startTimeParts[0] ?? 0) * 100000000);
        $this->startTimeStamp = (int)($startTimeParts[1] ?? 0);
    }

    /**
     * Stop (save the stop date time checkpoint)
     *
     * @return void
     */
    public function stop(): void
    {
        $endTime = \microtime();
        $endTimeParts = \explode(' ', $endTime);
        $this->endMicroTimeStamp = (int)(($endTimeParts[0] ?? 0) * 100000000);
        $this->endTimeStamp = (int)($endTimeParts[1] ?? 0);
    }

    /**
     * Get the delta between the start date time checkpoint and the stop date time checkpoint.
     *
     * @return string
     */
    public function delta(): string
    {
        if ($this->endMicroTimeStamp < $this->startMicroTimeStamp) {
            $endTimeStampReduced = $this->endTimeStamp - 1;
            $deltaMicroTimeStamp = ($this->endMicroTimeStamp + 100000000) - $this->startMicroTimeStamp;
            $deltaTimeStamp = $endTimeStampReduced - $this->startTimeStamp;
        } else {
            $deltaTimeStamp = $this->endTimeStamp - $this->startTimeStamp;
            $deltaMicroTimeStamp = $this->endMicroTimeStamp - $this->startMicroTimeStamp;
        }

        $length = \strlen((string)$deltaMicroTimeStamp);
        if ($length < 8) {
            $deltaMicroTimeStamp = \str_repeat('0', 8 - $length) . $deltaMicroTimeStamp;
        }

        $result = $deltaTimeStamp . '.' . $deltaMicroTimeStamp;

        $this->clear();

        return $result;
    }

    /**
     * Clear saved values.
     *
     * @return bool
     */
    public function clear(): bool
    {
        $this->startTimeStamp = 0;
        $this->startMicroTimeStamp = 0;
        $this->endTimeStamp = 0;
        $this->endMicroTimeStamp = 0;

        return true;
    }
}
