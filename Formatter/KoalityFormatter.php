<?php

/**
 * Class KoalityFormatter
 *
 * This class is used to create an IETF conform health JSON that can be
 * read by koality.io.
 *
 * @see https://tools.ietf.org/html/draft-inadarei-api-health-check-05
 *
 * @package Koality\ShopwarePlugin\Formatter
 *
 * @author Nils Langner <nils.langner@leankoala.com>
 * created 2020-12-28
 */
class KoalityFormatter_KoalityFormatter
{
    /**
     * @var KoalityFormatter_Result[]
     */
    private $results = [];

    /**
     * @var KoalityFormatter_Error
     */
    private $errors = [];

    /**
     * Add a new result.
     *
     * If the status of the result is "fail" the whole check will be marked as failed.
     *
     * @param KoalityFormatter_Result $result
     */
    public function addResult(KoalityFormatter_Result $result)
    {
        $this->results[] = $result;
    }

    public function addError(KoalityFormatter_Error $error)
    {
        $this->errors[] = $error;
    }

    /**
     * Return an IETF conform result array with all sub results.
     *
     * @return array
     */
    public function getFormattedResults()
    {
        $formattedResult = [];
        $checks = [];
        $status = KoalityFormatter_Result::STATUS_PASS;

        foreach ($this->results as $result) {
            $check = [
                'status' => $result->getStatus(),
                'output' => $result->getMessage()
            ];

            if (is_numeric($result->getLimit())) {
                $check['limit'] = $result->getLimit();
            }

            if (!is_null($result->getLimitType())) {
                $check['limitType'] = $result->getLimitType();
            }

            if (!is_null($result->getObservedValue())) {
                $check['observedValue'] = $result->getObservedValue();
            }

            if (!is_null($result->getObservedValueUnit())) {
                $check['observedUnit'] = $result->getObservedValueUnit();
            }

            if (!is_null($result->getObservedValuePrecision())) {
                $check['observedValuePrecision'] = $result->getObservedValuePrecision();
            }

            if (!is_null($result->getType())) {
                $check['metricType'] = $result->getType();
            }

            $attributes = $result->getAttributes();
            if (count($attributes) > 0) {
                $check['attributes'] = $attributes;
            }

            $checks[$result->getKey()] = $check;

            if ($result->getStatus() == KoalityFormatter_Result::STATUS_FAIL) {
                $status = KoalityFormatter_Result::STATUS_FAIL;
            }
        }

        $formattedResult['status'] = $status;
        $formattedResult['output'] = $this->getOutput($status);

        $formattedResult['checks'] = $checks;

        $errorBlock = $this->getErrorBlock();
        if(count($errorBlock) > 0) {
            $formattedResult['errors'] = $errorBlock;
        }

        $formattedResult['info'] = $this->getInfoBlock();

        return $formattedResult;
    }

    /**
     * Get the output string depending on the given status.
     *
     * @param string $status
     *
     * @return string
     */
    private function getOutput($status)
    {
        if ($status === KoalityFormatter_Result::STATUS_PASS) {
            return 'All Shopware 5 health metrics passed.';
        } else {
            return 'Some Shopware 5 health metrics failed: ';
        }
    }

    private function getErrorBlock()
    {
        $block = [];
        foreach ($this->errors as $error) {
            /** @var KoalityFormatter_Error $error */
            $block[] = ['collector' => $error->getCollector(), 'message' => $error->getMessage()];
        }
        return $block;
    }

    /**
     * Return the info block for the JSON output
     *
     * @return string[]
     */
    private function getInfoBlock()
    {
        return [
            'creator' => 'koality.io Shopware 5 Plugin',
            'version' => '1.0.0',
            'plugin_url' => 'https://www.koality.io/plugins/shopware'
        ];
    }
}
