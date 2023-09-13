<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Api;

/**
 * The planner interface which allows
 *  - check if temporary table with buffer products exists
 *  - create if not exists (@todo: Looks like another responsibility)
 *  - check if we have buffer products for import
 *  - if buffer products exists, publish messages to RabbitMQ for importing the buffer product
 *    [One message to RabbitMQ queue for the one buffer product]
 *  - Mark the planned for import product
 *    with flag `doctor_dogg_is_planned_for_import` [BufferProductInterface::IS_PLANNED_FOR_IMPORT_ID]
 */
interface ProductImportTaskPlannerInterface
{
    /**
     * Run process:
     *  - check if temporary table with buffer products exists
     *  - create if not exists
     *  - check if we have buffer products for import
     *  - if we have, publish messages to RabbitMQ for importing the buffer product
     *    [One message to RabbitMQ queue for the one buffer product]
     *  - Mark the planned for import product
     *    with flag `doctor_dogg_is_planned_for_import` [BufferProductInterface::IS_PLANNED_FOR_IMPORT_ID]
     *
     * @return void
     */
    public function startPlanningProcess(): void;
}
