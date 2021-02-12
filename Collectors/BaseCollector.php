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
}
