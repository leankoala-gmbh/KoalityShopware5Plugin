<?php

/**
 * Class FailedCronjobs
 *
 * This collector takes care for the failed cronjobs.
 *
 * @author Nils Langner <nils.langner@leankoala.com>
 * created 2021-02-13
 */
class KoalityCollector_FailedCronjobs extends KoalityCollector_BaseCollector
{
    protected $messageSuccess = 'There were not too many errors in the execution of the cronjobs.';
    protected $messageFailure = 'There were too many errors in the execution of the cronjobs.';

    protected $configThresholdKey = 'failedCronjobs';

    protected $resultKey = 'cronjobs.failed';

    protected $resultUnit = 'cronjobs';

    /**
     * @inheritDoc
     *
     * @throws KoalityCollector_NotImplementedException
     */
    protected function getCurrentValue()
    {
        throw new KoalityCollector_NotImplementedException('This collector is not implemented yet');
    }
}
