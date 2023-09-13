<?php

declare(strict_types=1);

try {
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $stopWatchInterface = $objectManager->create(\DoctorDogg\StopWatch\Api\StopWatchInterface::class);
    $stopWatchInterface->start();
    $stopWatchInterface->stop();
    $deltaString = $stopWatchInterface->delta();
} catch (\Throwable $throwable) {
    $a = 10;
}

exit;
