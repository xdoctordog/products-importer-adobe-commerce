<?php

declare(strict_types=1);

use Magento\Framework\Component\ComponentRegistrar;

/**
 * Log message preparer extension.
 */
ComponentRegistrar::register(ComponentRegistrar::MODULE, 'DoctorDogg_LogMessagePreparer', __DIR__);
