<?php

use KoalityShopware5Monitoring\KoalityShopware5Monitoring;
use Shopware\Components\Plugin\ConfigReader;

Shopware()->Application()->Loader()->registerNamespace('KoalityCollector', __DIR__ . '/../../Collectors/');
Shopware()->Application()->Loader()->registerNamespace('KoalityFormatter', __DIR__ . '/../../Formatter/');

/**
 * Class HealthController
 *
 * @author Nils Langner <nils.langner@leankoala.com>
 * created 2021-02-10
 */
class Shopware_Controllers_Frontend_Health extends Enlight_Controller_Action
{
    /**
     * @var KoalityCollector_Collector[]
     */
    private $collectors = [];

    /**
     * @var string[]
     */
    private $config;

    /**
     * Run all validations and return them as IETF health check format.
     */
    public function indexAction()
    {
        try {
            $this->initConfig();
            $this->assertApiKeyAllowed();

            $this->initCollectors();

            $results = $this->validateCollectors();

            $formatter = new KoalityFormatter_KoalityFormatter();

            foreach ($results as $result) {
                if ($result instanceof KoalityFormatter_Result) {
                    $formatter->addResult($result);
                } elseif ($result instanceof KoalityFormatter_Error) {
                    $formatter->addError($result);
                }
            }

            $formattedResults = $formatter->getFormattedResults();

            echo json_encode($formattedResults, JSON_PRETTY_PRINT);
            die;

        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            die;
        }
    }

    /**
     * Initialize all collectors.
     *
     * @throws Exception
     */
    private function initCollectors()
    {
        $modelManager = $this->getModelManager();

        $this->collectors[] = new KoalityCollector_OrdersPerHour($this->config, $modelManager);
        $this->collectors[] = new KoalityCollector_ActiveProducts($this->config, $modelManager);
        $this->collectors[] = new KoalityCollector_ImagelessProducts($this->config, $modelManager);

        $this->collectors[] = new KoalityCollector_FailedCronjobs($this->config, $modelManager);
        $this->collectors[] = new KoalityCollector_VisitorsPerHour($this->config, $modelManager);
        $this->collectors[] = new KoalityCollector_UpdatablePlugins($this->config, $modelManager);
    }

    /**
     * Initialize the plugin configuration.
     *
     * @throws Exception
     */
    private function initConfig()
    {
        /** @var ConfigReader $configReader */
        $configReader = $this->get('shopware.plugin.config_reader');
        $this->config = $configReader->getByPluginName(KoalityShopware5Monitoring::PLUGIN_NAME);
    }

    /**
     * Run all collector validations and return an array of results.
     *
     * @return KoalityFormatter_Result[]
     */
    private function validateCollectors()
    {
        $results = [];
        foreach ($this->collectors as $collector) {
            try {
                $result = $collector->validate();
            } catch (\Exception $e) {
                var_dump('hier');
                $results[]  = new KoalityFormatter_Error(get_class($collector), $e->getMessage());
            }
            if ($result instanceof KoalityFormatter_Result) {
                $results[] = $result;
            } else {
                // @todo handle collectors that do not return a KoalityFormatter_Result
            }
        }

        return $results;
    }

    /**
     * Throw an exception if the user is not allowed to see the health data.
     *
     * @throws Exception
     */
    private function assertApiKeyAllowed()
    {
        $requestApiKey = $this->request->get('apiKey');

        if (!$requestApiKey) {
            throw new \RuntimeException('No apiKey as request parameter set.');
        }

        if (!array_key_exists(KoalityShopware5Monitoring::CONFIG_KEY_API_KEY, $this->config)) {
            throw new \RuntimeException('No API key found in configuration');
        }

        if ($this->config[KoalityShopware5Monitoring::CONFIG_KEY_API_KEY] != $requestApiKey) {
            throw new \RuntimeException('The given api key is not valid.');
        }
    }
}
