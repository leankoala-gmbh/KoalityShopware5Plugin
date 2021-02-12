<?php

namespace KoalityMonitoring\Tests;

use KoalityMonitoring\KoalityMonitoring as Plugin;
use Shopware\Components\Test\Plugin\TestCase;

class PluginTest extends TestCase
{
    protected static $ensureLoadedPlugins = [
        'KoalityMonitoring' => []
    ];

    public function testCanCreateInstance()
    {
        /** @var Plugin $plugin */
        $plugin = Shopware()->Container()->get('kernel')->getPlugins()['KoalityMonitoring'];

        $this->assertInstanceOf(Plugin::class, $plugin);
    }
}
