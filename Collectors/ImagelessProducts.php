<?php

/**
 * Class ImagelessProducts
 *
 * This collectors fails if there are too many products in the shop that have no image.
 *
 * @author Nils Langner <nils.langner@leankoala.com>
 * created 2021-02-14
 */
class KoalityCollector_ImagelessProducts extends KoalityCollector_BaseCollector
{
    protected $messageSuccess = 'Not too many products without image found.';
    protected $messageFailure = 'There are too many products without images in the shop.';

    protected $configThresholdKey = 'imagelessProducts';

    protected $resultKey = 'products.imageless';

    protected $resultUnit = 'products';

    /**
     * Returns the number of products without images.
     *
     * @inheritDoc
     */
    protected function getCurrentValue()
    {
        $query = "SELECT count(*) FROM `s_articles` JOIN s_articles_img as articleImage ON articleImage.articleID = s_articles.id " .
            "WHERE s_articles.active = 1 AND articleImage.id IS NULL";

        $imageCount = $this->findOneBy($query);
        return (int)$imageCount;
    }
}
