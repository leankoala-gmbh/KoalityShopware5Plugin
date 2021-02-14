<?php

/**
 * Class VisitorsPerHour
 *
 * This collector fails if the the number of visitors in the last 60min is too low.
 *
 * The threshold can be defined for weekend, rush hour and normal times.
 *
 * @author Nils Langner <nils.langner@leankoala.com>
 * created 2021-02-13
 */
class KoalityCollector_VisitorsPerHour extends KoalityCollector_BaseCollector
{
    protected $messageSuccess = 'In the last hours there were enough visitors in the online store.';
    protected $messageFailure = 'In the last hours there were too few visitors in the online store.';

    protected $configThresholdKey = 'visitorsPer';

    protected $resultKey = 'shop.visitors';

    protected $resultUnit = 'visitors';

    /**
     * Returns the visitors within the last 60 minutes.
     *
     * @inheritDoc
     *
     * @throws KoalityCollector_NotImplementedException
     */
    protected function getCurrentValue()
    {
        throw new KoalityCollector_NotImplementedException('This collector is not implemented yet.');
    }

    /**
     * Return the threshold for the current date time.
     *
     * Like in OrdersByHour there are two time intervals rush hour and the other opening times. It is also
     * important if it is weekend or not.
     *
     * @inheritDoc
     *
     * @throws KoalityCollector_NotImplementedException
     */
    protected function getThreshold($key, $fallbackValue = null)
    {
        throw new KoalityCollector_NotImplementedException('This collector is not implemented yet.');
    }
}
