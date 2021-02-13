<?php

/**
 * Class UpdatablePlugins
 *
 * This collector takes care for the orders done within the last hour. It alerts if they fall
 * under a configured threshold.
 *
 * The threshold can be defined for weekend, rush hour and normal times.
 *
 * @author Nils Langner <nils.langner@leankoala.com>
 * created 2021-02-12
 */
class KoalityCollector_UpdatablePlugins extends KoalityCollector_BaseCollector
{
    const KEY_ORDERS_PER_RUSHHOUR = 'updatablePlugins';

    /**
     * @inheritDoc
     */
    public function validate()
    {
        $this->refreshPlugins();

        $updatablePlugins = $this->getUpdatablePlugins();

        return;

        $orderCount = $this->getOrderCount();
        $orderThreshold = $this->getOrderThreshold();

        if ($orderCount < $orderThreshold) {
            $orderResult = new KoalityFormatter_Result(KoalityFormatter_Result::STATUS_FAIL, KoalityFormatter_Result::KEY_ORDERS_TOO_FEW, 'There were too few orders within the last hour.');
        } else {
            $orderResult = new KoalityFormatter_Result(KoalityFormatter_Result::STATUS_PASS, KoalityFormatter_Result::KEY_ORDERS_TOO_FEW, 'There were enough orders within the last hour.');
        }

        $orderResult->setLimit($orderThreshold);
        $orderResult->setObservedValue($orderCount);
        $orderResult->setObservedValuePrecision(2);
        $orderResult->setObservedValueUnit('orders');
        $orderResult->setLimitType(KoalityFormatter_Result::LIMIT_TYPE_MIN);
        $orderResult->setType(KoalityFormatter_Result::TYPE_TIME_SERIES_NUMERIC);

        return $orderResult;
    }

    /**
     * Return the sales threshold depending on the current time.
     *
     * @return int
     */
    private function getUpdateablePluginsThreshold()
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
     *
     * @throws Exception
     */
    private function getUpdatablePlugins()
    {
        $updateContext = $this->getPluginContext(self::CONTEXT_PLUGIN_UPDATE);

        $plugins = $updateContext->getPlugins();

        foreach ($plugins as $plugin) {
            if ($plugin->isActive()) {
                var_dump($plugin->getTechnicalName());
                var_dump($plugin->getAvailableVersion());
                var_dump($plugin->getVersion());
            }
        }
    }

    private function refreshPlugins()
    {
        /** @var \Shopware\Bundle\PluginInstallerBundle\Service\InstallerService $pluginManager */
        $pluginManager = Shopware()->Container()->get('shopware_plugininstaller.plugin_manager');
        $pluginManager->refreshPluginList();
    }
}
