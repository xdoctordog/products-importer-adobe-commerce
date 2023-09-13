<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\Config\Reader\ConfigReader;

use \Magento\Framework\Serialize\Serializer\Json;
use \Psr\Log\LoggerInterface;
use \DoctorDogg\ProductsImporter\Model\Config\Reader\ConfigReader;
use \DoctorDogg\ProductsImporter\Model\DoctorDoggProductsImporterExtensionInterface as DoctorDoggProductsImporter;

/**
 * Class decorator|proxy.
 *
 * Class adds logging about the value which is getting from the admin area settings.
 *
 * @author Morozov "Doctor Dogg" Dmitriy <dmitriy.marozau@gmail.com>
 *
 * @method array getProductRequiredCoreAttributes(string|null $callerMethod = null)
 * @method array getProductAdditionalAttributes(string|null $callerMethod = null)
 * @method bool|null getImportKnifeSwitcher(string|null $callerMethod = null)
 * @method int|null getNumberProductsScheduledAtTime(string|null $callerMethod = null)
 * @method bool|null getCheckProductExistsAfterImport(string|null $callerMethod = null)
 * @method int|null getNumberColumnsInTemporaryBufferTable(string|null $callerMethod = null)
 * @method int|null getDefaultFieldLengthTempProductTable(string|null $callerMethod = null)
 *
 * @TODO: Probably this should be replaced with more elegant solution.
 * @TODO: But i really like this solution :)
 */
class ConfigReaderLoggerDecorator
{
    /**
     * @const string BOOLEAN_ON_VALUE
     */
    public const BOOLEAN_ON_VALUE = 'ON';

    /**
     * @const string BOOLEAN_OFF_VALUE
     */
    public const BOOLEAN_OFF_VALUE = 'OFF';

    /**
     * @var ConfigReader
     */
    private ConfigReader $configReader;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var Json
     */
    private Json $json;

    /**
     * Constructor.
     *
     * @param ConfigReader $configReader
     * @param Json $json
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConfigReader $configReader,
        Json $json,
        LoggerInterface $logger
    ) {
        $this->configReader = $configReader;
        $this->logger = $logger;
        $this->json = $json;
    }

    /**
     * Magic method works as a wrapper with possibility to log every real call to the method.
     *
     * @param string $methodName
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $methodName, array $arguments) : mixed
    {
        if (\method_exists($this->configReader, $methodName)) {

            $caller = '';
            $length = \count($arguments);

            $lastArgumentValue = null;
            if ($length > 0) {
                $lastArgumentValue = $arguments[$length - 1] ?? null;
                /**
                 * @TODO: Not sure if this is the best solution.
                 * @TODO: When we are unsetting the last argument because it is the caller method.
                 */
                unset($arguments[$length - 1]);
            }
            if (!$lastArgumentValue) {
                $backtrace = \debug_backtrace();
                if (\is_array($backtrace)) {
                    $caller = $backtrace[0]['file'] ?? '';
                }
            } else {
                $callerMethod = $lastArgumentValue;
                $caller = $callerMethod . '()';
            }

            $value = \call_user_func_array([$this->configReader, $methodName], \array_values($arguments));

            $valueString = $value;

            $valueString = match (true) {
                \is_array($valueString) => $this->json->serialize($valueString),
                (true === $value) => static::BOOLEAN_ON_VALUE,
                (false === $value) => static::BOOLEAN_OFF_VALUE,
                \is_scalar($valueString) => $valueString,
                default => '<can\'t stringify returned data>',
            };

            $logString = PHP_EOL .
                '[' . DoctorDoggProductsImporter::NAME . ']: ' . $caller . ' '  . PHP_EOL
                . ' ' . $methodName . '()' . '[ return ' . $valueString . ' ] '  . PHP_EOL
                . (string)\date(DATE_RFC2822) . PHP_EOL;

            $this->logger->info($logString);

            return $value;
        }

        return null;
    }
}
