<?php

namespace KoalityMonitoring;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Models\Shop\DetachedShop;
use Shopware\Models\Shop\Shop;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class KoalityMonitoring
 *
 * @package KoalityMonitoring
 *
 * @author Nils Langner <nils.langner@leankoala.com>
 * created 2021-02-10
 */
class KoalityMonitoring extends Plugin
{
    const VERSION = '1.0.0';

    const CONFIG_KEY_API_KEY = 'apiKey';

    const PLUGIN_NAME = 'KoalityMonitoring';

    /**
     * @inheritDoc
     */
    public function __install(InstallContext $context)
    {
        parent::install($context);

        $shop = false;
        if ($this->container->initialized('shop')) {
            $shop = $this->container->get('shop');
        }

        if (!$shop) {
            /** @var DetachedShop $shop */
            $shop = $this->container->get('models')->getRepository(Shop::class)->getActiveDefault();
        }

        /** @var \Shopware\Models\Plugin\Plugin $plugin */
        $plugin = Shopware()->Models()->getRepository(\Shopware\Models\Plugin\Plugin::class)->findOneBy(['name' => self::PLUGIN_NAME]);

        /** @var Plugin\ConfigWriter $configWriter */
        $configWriter = $this->container->get('shopware.plugin.config_writer');

        $elements = [self::CONFIG_KEY_API_KEY =>  $this->createGuid()];
        $configWriter->savePluginConfig($plugin, $elements, $shop);
    }

    /**
     * Create an UUID for the plugins access.
     *
     * @return string
     */
    private function createGuid()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('koality_monitoring.plugin_dir', $this->getPath());
        parent::build($container);
    }

}
