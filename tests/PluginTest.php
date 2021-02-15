<?php

namespace KoalityShopware5Monitoring\Tests;

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
