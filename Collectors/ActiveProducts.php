<?php

/**
 * Class ActiveProducts
 *
 * This collector takes care for the active products within the shop. Monitoring them is the key to find import
 * errors where many products disappear and it does not get noticed.
 *
 * The threshold can be defined for weekend, rush hour and normal times.
 *
 * @author Nils Langner <nils.langner@leankoala.com>
 * created 2021-02-13
 */
class KoalityCollector_ActiveProducts extends KoalityCollector_BaseCollector
{
    const KEY_ACTIVE_PRODUCTS = 'activeProducts';

    /**
     * @inheritDoc
     */
    public function validate()
    {
        $activeProductsCount = $this->getActiveProductCount();
        $activeProductsThreshold = $this->getActiveProductThreshold();

        if ($activeProductsCount < $activeProductsThreshold) {
            $orderResult = new KoalityFormatter_Result(KoalityFormatter_Result::STATUS_FAIL, KoalityFormatter_Result::KEY_PRODUCTS_ACTIVE, 'There are too few active projects in your shop.');
        } else {
            $orderResult = new KoalityFormatter_Result(KoalityFormatter_Result::STATUS_PASS, KoalityFormatter_Result::KEY_PRODUCTS_ACTIVE, 'There are enough active projects in your shop.');
        }

        $orderResult->setLimit($activeProductsThreshold);
        $orderResult->setObservedValue($activeProductsCount);
        $orderResult->setObservedValuePrecision(2);
        $orderResult->setObservedValueUnit('products');
        $orderResult->setLimitType(KoalityFormatter_Result::LIMIT_TYPE_MIN);
        $orderResult->setType(KoalityFormatter_Result::TYPE_TIME_SERIES_NUMERIC);

        return $orderResult;
    }

    /**
     * Return the active products threshold.
     *
     * @return int
     */
    private function getActiveProductThreshold()
    {
        return (int) $this->config[self::KEY_ACTIVE_PRODUCTS];
    }

    /**
     * Return the number of active products in the shop.
     *
     * @return int
     */
    private function getActiveProductCount()
    {
        $query = "select count(*) from s_articles where active = 1;";
        $orderCount = $this->findOneBy($query);
        return (int)$orderCount;
    }
}
