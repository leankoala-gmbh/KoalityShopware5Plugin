<?php

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
     *
     * @throws KoalityCollector_NotImplementedException
     */
    protected function getCurrentValue()
    {
        /**
         * This function must return an array like ['TechnicalName1', 'TechnicalName2']
         *
         * return ['Plugin1', 'Plugin2']
         */
        throw new KoalityCollector_NotImplementedException('This collector is not implemented yet.');
    }
}
