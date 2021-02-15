<?php

/**
 * Class KoalityFormatter_Error
 *
 *
 * @author Nils Langner <nils.langner@leankoala.com>
 * created 2021-02-15
 */
class KoalityFormatter_Error
{
    /**
     * @var string
     */
    private $collector;

    /**
     * @var string
     */
    private $message;

    /**
     * KoalityFormatter_Error constructor.
     *
     * @param string $collector
     * @param string $message
     */
    public function __construct($collector, $message)
    {
        $this->collector = $collector;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getCollector()
    {
        return $this->collector;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
