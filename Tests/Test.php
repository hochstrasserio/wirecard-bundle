<?php

namespace Hochstrasser\WirecardBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Test extends WebTestCase
{
    /**
     * Test that kernel can be created with bundle without throwing exceptions
     *
     * @test
     */
    function kernelCanBeCreatedWithBundleEnabled()
    {
        $kernel = static::createKernel();
        $this->assertNotNull($kernel);
    }

    /**
     * @test
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    function kernelThrowsExceptionWhenConfigInvalid()
    {
        $kernel = new \InvalidAppKernel('dev', true);
        $kernel->boot();
    }
}
