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

    protected $messageSuccess = 'There were enough orders within the last hour.';
    protected $messageFailure = 'There were too few orders within the last hour.';

    protected $resultKey = KoalityFormatter_Result::KEY_ORDERS_TOO_FEW;
    protected $resultLimitType = KoalityFormatter_Result::LIMIT_TYPE_MIN;

    protected $resultUnit = 'orders';

    /**
     * Return the sales threshold depending on the current time.
     *
     * @inheritDoc
     */
    protected function getThreshold($key, $fallbackValue = null)
    {
        $config = $this->config;

        if ($this->isRushhour()) {
            return (int)$config[self::KEY_ORDERS_PER_RUSHHOUR];
        } else {
            return (int)$config[self::KEY_ORDERS_NORMAL];
        }
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
