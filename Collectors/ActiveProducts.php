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
    protected $messageSuccess = 'There are enough active projects in your shop.';
    protected $messageFailure = 'There are too few active projects in your shop.';

    protected $configThresholdKey = 'activeProducts';

    protected $resultKey =KoalityFormatter_Result::KEY_PRODUCTS_ACTIVE;

    protected $resultUnit = 'products';
    protected $resultLimitType = KoalityFormatter_Result::LIMIT_TYPE_MIN;

    /**
     * Return the number of active products in the shop.
     *
     * @return int
     */
    protected function getCurrentValue()
    {
        $query = "select count(*) from s_articles where active = 1;";
        $orderCount = $this->findOneBy($query);
        return (int)$orderCount;
    }
}
