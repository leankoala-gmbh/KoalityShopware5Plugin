<?php

namespace KoalityShopware5Monitoring;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class KoalityShopware5Monitoring
 *
 * @package KoalityShopware5Monitoring
 *
 * @author Nils Langner <nils.langner@leankoala.com>
 * created 2021-02-10
 */
class KoalityShopware5Monitoring extends Plugin
{
    const VERSION = '1.0.0';

    const CONFIG_KEY_API_KEY = 'apiKey';

    const PLUGIN_NAME = 'KoalityShopware5Monitoring';

    /**
     * @inheritDoc
     */
    public function install(InstallContext $context)
    {
        parent::install($context);

        $shop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->findOneBy(array('default' => true));
        $pluginManager = Shopware()->Container()->get('shopware.plugin_manager');
        $plugin = $pluginManager->getPluginByName(self::PLUGIN_NAME);
        $pluginManager->saveConfigElement($plugin, self::CONFIG_KEY_API_KEY, $this->createGuid(), $shop);
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

    /**
     * Overwrite the clear cache process from parent
     *
     * @param Plugin\Context\UninstallContext $context
     */
    public function uninstall(UninstallContext $context)
    {
    }
}
