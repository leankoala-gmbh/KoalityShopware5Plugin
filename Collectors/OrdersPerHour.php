<?php

/**
 * Class OrdersByHour
 *
 * This collector takes care for the orders done within the last hour. It alerts if they fall
 * under a configured threshold.
 *
 * The threshold can be defined for weekend, rush hour and normal times.
 *
 * @author Nils Langner <nils.langner@leankoala.com>
 * created 2021-02-12
 */
class KoalityCollector_OrdersPerHour extends KoalityCollector_BaseCollector
{
    const KEY_ORDERS_PER_RUSHHOUR = 'ordersPerHourRushHour';
    const KEY_ORDERS_NORMAL = 'ordersPerHourNormal';
    const KEY_INCLUDE_WEEKENDS = 'includeWeekends';
    const KEY_RUSH_HOUR_BEGIN = 'rushHourBegin';
    const KEY_RUSH_HOUR_END = 'rushHourEnd';

    protected $messageSuccess = 'There were enough orders within the last hour.';
    protected $messageFailure = 'There were too few orders within the last hour.';

    protected $resultKey = KoalityFormatter_Result::KEY_ORDERS_TOO_FEW;
    protected $resultLimitType = KoalityFormatter_Result::LIMIT_TYPE_MIN;

    protected $resultUnit = 'order';

    /**
     * Return the sales threshold depending on the current time.
     *
     * @inheritDoc
     */
    protected function getThreshold($key, $fallbackValue = null)
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
                return (int)$config[self::KEY_ORDERS_PER_RUSHHOUR];
            }
        }

        return (int)$config[self::KEY_ORDERS_NORMAL];
    }

    /**
     * Return the number of orders that were processed in the last one hour.
     *
     * @return int
     */
    protected function getCurrentValue()
    {
        $intervalInHours = 1;
        $date = date('Y-m-d H:i:s', strtotime('-' . $intervalInHours . ' hour'));
        $query = 'select count(*) from s_order where ordertime > "' . $date . '" and status = 0;';

        $orderCount = $this->findOneBy($query);

        return (int)$orderCount;
    }
}
