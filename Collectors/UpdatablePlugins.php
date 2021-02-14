<?php

use Shopware\Bundle\PluginInstallerBundle\Context\UpdateListingRequest;
use Shopware\Bundle\PluginInstallerBundle\Struct\UpdateResultStruct;

/**
 * Class UpdatablePlugins
 *
 * This collector takes care of the plugins that need to be updated.
 *
 * @author Nils Langner <nils.langner@leankoala.com>
 * created 2021-02-12
 */
class KoalityCollector_UpdatablePlugins extends KoalityCollector_BaseCollector
{
    protected $messageSuccess = 'There are not too many plugins that need an update.';
    protected $messageFailure = 'There are too many plugins that need an update.';

    protected $configThresholdKey = 'updatablePlugins';

    protected $resultKey = 'plugins.updatable';

    protected $resultUnit = 'plugins';

    /**
     * Returns the plugins that need to be updated.
     *
     * @inheritDoc
     */
    protected function getCurrentValue()
    {
        $container = Shopware()->Container();

        $version = $this->getShopwareVersion();

        $plugins = $container->get('shopware_plugininstaller.plugin_service_local')->getPluginsForUpdateCheck();
        $domain = $container->get('shopware_plugininstaller.account_manager_service')->getDomain();
        $service = $container->get('shopware_plugininstaller.plugin_service_view');

        $request = new UpdateListingRequest('', $version, $domain, $plugins);
        /** @var UpdateResultStruct $updates */

        $updates = $service->getUpdates($request);
        $plugins = $updates->getPlugins();

        $updatablePlugins = [];

        foreach ($plugins as $plugin) {
            $updatablePlugins[] = $plugin->getTechnicalName();
        }

        return $updatablePlugins;
    }
}
