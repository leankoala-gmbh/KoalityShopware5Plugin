<?php

use Shopware\Bundle\PluginInstallerBundle\Context\BaseRequest;
use Shopware\Bundle\PluginInstallerBundle\Context\ListingRequest;
use Shopware\Bundle\PluginInstallerBundle\Context\UpdateListingRequest;
use Shopware\Bundle\PluginInstallerBundle\Service\PluginViewService;
use Shopware\Bundle\PluginInstallerBundle\Struct\PluginStruct;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Shop\Shop;

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
     * Return the default shop object.
     *
     * @return Shop
     */
    protected function getDefaultShop()
    {
        $shopRepo = Shopware()->Models()->getRepository(Shop::class);

        /** @var Shop $shop */
        $shop = $shopRepo->findOneBy(array('default' => 1));

        return $shop;
    }

    /**
     * Return the default shops locale.
     *
     * If the value is not set the fallback locale from the function argument is taken.
     *
     * @param string $fallbackLocale
     * @return string
     */
    private function getShopLocale($fallbackLocale = self::LOCALE_DEFAULT)
    {
        $defaultShop = $this->getDefaultShop();

        if (!is_null($defaultShop)) {
            $shopLocale = $defaultShop->getLocale()->getLocale();
        }

        if (empty($shopLocale)) {
            $shopLocale = $fallbackLocale;
        }

        return $shopLocale;
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
     * Return a valid context for the plugin view service.
     *
     * @param string $contextType
     *
     * @return BaseRequest
     *
     * @throws Exception
     */
    protected function getPluginContext($contextType = self::CONTEXT_PLUGIN_LISTING)
    {
        switch ($contextType) {
            case self::CONTEXT_PLUGIN_LISTING:
                return new ListingRequest($this->getShopLocale(), $this->getShopLocale(), null, null, [], []);
            case self::CONTEXT_PLUGIN_UPDATE:
                $plugins = $this->getPlugins();
                return new UpdateListingRequest($this->getShopLocale(), $this->getShopLocale(), null, $plugins);
        }

        throw new \Exception('The given context type "' . $contextType . '" is not known.');
    }

    /**
     * Return a list of plugins.
     *
     * This list contains all plugins no matter if they are active or installed.
     *
     * @return PluginStruct[]
     *
     * @throws Exception
     */
    protected function getPlugins()
    {
        /** @var PluginViewService $pluginService */
        $pluginService = Shopware()->Container()->get('shopware_plugininstaller.plugin_service_view');
        return $pluginService->getLocalListing($this->getPluginContext());
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
            return $this[$key];
        } else {
            if (!is_null($fallbackValue)) {
                return $fallbackValue;
            } else {
                throw new RuntimeException('No configuration or fallback value found for key ' . $key . '.');
            }
        }
    }
}
