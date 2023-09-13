<?php

declare(strict_types=1);

/**
 * The extension helper which allows to keeps track of time elapsed from the beginning of a procedure to its completion.
 */
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'DoctorDogg_StopWatch',
    __DIR__
);
