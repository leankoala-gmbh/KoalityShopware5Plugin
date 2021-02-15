<?php

namespace KoalityMonitoring\Tests;

use KoalityMonitoring\KoalityShopware5Monitoring as Plugin;
use Shopware\Components\Test\Plugin\TestCase;

class PluginTest extends TestCase
{
    protected static $ensureLoadedPlugins = [
        'KoalityMonitoring' => []
    ];

    public function testCanCreateInstance()
    {
    }
}
