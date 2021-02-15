<?php

use Shopware\Components\Model\ModelManager;

/**
 * Class Koality_BaseCollector
 *
 * This is the basic class all collectors extend with the basic functionality to
 * communicate with the Shopware5 shop.
 *
 * @author Nils Langner <nils.langner@leankoala.com>
 * created 2021-02-12
 */
abstract class KoalityCollector_BaseCollector implements KoalityCollector_Collector
{
    const LOCALE_DEFAULT = 'de_DE';

    const CONTEXT_PLUGIN_LISTING = 'listing';
    const CONTEXT_PLUGIN_UPDATE = 'update';

    const KEY_INCLUDE_WEEKENDS = 'includeWeekends';
    const KEY_RUSH_HOUR_BEGIN = 'rushHourBegin';
    const KEY_RUSH_HOUR_END = 'rushHourEnd';

    protected $configThresholdKey;
    protected $configThresholdFallback = 0;

    protected $messageSuccess;
    protected $messageFailure;

    protected $resultKey;
    protected $resultUnit;

    protected $resultPrecision = 2;
    protected $resultLimitType = KoalityFormatter_Result::LIMIT_TYPE_MAX;
    protected $resultType = KoalityFormatter_Result::TYPE_TIME_SERIES_NUMERIC;

    protected $resultDetailKey = 'details';

    /**
     * @var ModelManager
     */
    protected $modelManager;

    /**
     * @var string[]
     */
    protected $config;

    /**
     * KoalityCollector_BaseCollector constructor.
     *
     * @param array $config
     * @param ModelManager $modelManager
     */
    public function __construct($config, ModelManager $modelManager)
    {
        $this->modelManager = $modelManager;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
        $threshold = $this->getThreshold($this->configThresholdKey, $this->configThresholdFallback);
        $currentValue = $this->getCurrentValue();

        $valueDetails = false;
        if (is_array($currentValue)) {
            $valueDetails = $currentValue;
            $currentValue = count($currentValue);
        }

        if ($currentValue < $threshold) {
            $orderResult = new KoalityFormatter_Result(KoalityFormatter_Result::STATUS_FAIL, $this->resultKey, $this->messageFailure);
        } else {
            $orderResult = new KoalityFormatter_Result(KoalityFormatter_Result::STATUS_PASS, $this->resultKey, $this->messageSuccess);
        }

        if ($valueDetails) {
            $orderResult->addAttribute($this->resultDetailKey, $valueDetails);
        }

        $orderResult->setLimit($threshold);
        $orderResult->setObservedValue($currentValue);
        $orderResult->setObservedValuePrecision($this->resultPrecision);
        $orderResult->setObservedValueUnit($this->resultUnit);
        $orderResult->setLimitType($this->resultLimitType);
        $orderResult->setType($this->resultType);

        return $orderResult;
    }

    /**
     * @return mixed
     */
    protected function getCurrentValue()
    {
        return null;
    }

    /**
     * Run a database query and return a single result.
     *
     * @param $query
     *
     * @return false|string|null
     */
    protected function findOneBy($query)
    {
        return Shopware()->Db()->fetchOne($query);
    }

    /**
     * Run a database query and return the results.
     *
     * @param $query
     * @return array|false
     */
    protected function findBy($query)
    {
        return Shopware()->Db()->fetchAll($query);
    }

    /**
     * Return the current Shopware version.
     *
     * @return string
     */
    protected function getShopwareVersion()
    {
        return Shopware()->Container()->getParameter('shopware.release.version');
    }

    /**
     * This function return a threshold from the configuration.
     *
     * If the value is not set in the config the fallback value is taken. If this is also not set
     * a RuntimeException is thrown.
     *
     * @param string $key
     * @param null|int $fallbackValue
     *
     * @return int
     *
     * @throws RuntimeException
     */
    protected function getThreshold($key, $fallbackValue = null)
    {
        if (array_key_exists($key, $this->config)) {
            return (int)$this->config[$key];
        } else {
            if (!is_null($fallbackValue)) {
                return $fallbackValue;
            } else {
                throw new RuntimeException('No configuration or fallback value found for key ' . $key . '.');
            }
        }
    }


    /**
     * Return true if the current time is within the rush hour.
     *
     * The time interval is defined in the plugins configuration.
     *
     * @return bool
     */
    protected function isRushhour()
    {
        $config = $this->config;

        $currentWeekDay = date('w');
        $isWeekend = ($currentWeekDay == 0 || $currentWeekDay == 6);

        $allowRushHour = !($isWeekend && !$config[self::KEY_INCLUDE_WEEKENDS]);

        if ($allowRushHour && array_key_exists(self::KEY_RUSH_HOUR_BEGIN, $config) && array_key_exists(self::KEY_RUSH_HOUR_END, $config)) {
            $beginHour = (int)substr($config[self::KEY_RUSH_HOUR_BEGIN], 11, 2) . substr($config[self::KEY_RUSH_HOUR_BEGIN], 14, 2);
            $endHour = (int)substr($config[self::KEY_RUSH_HOUR_END], 11, 2) . substr($config[self::KEY_RUSH_HOUR_END], 14, 2);

            $currentTime = (int)date('Hi');

            if ($currentTime < $endHour && $currentTime > $beginHour) {
                return true;
            }
        }

        return false;

    }
}
