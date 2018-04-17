<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Security;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigBuilderInterface;
use Netgen\Bundle\EzPublishBlockManagerBundle\Security\PolicyProvider;
use PHPUnit\Framework\TestCase;

final class PolicyProviderTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $configBuilderMock;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Security\PolicyProvider
     */
    private $policyProvider;

    public function setUp()
    {
        $this->configBuilderMock = $this->createMock(ConfigBuilderInterface::class);

        $this->policyProvider = new PolicyProvider();
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Security\PolicyProvider::addPolicies
     */
    public function testAddPolicies()
    {
        $this->configBuilderMock
            ->expects($this->once())
            ->method('addConfig')
            ->with(
                $this->equalTo(
                    [
                        'nglayouts' => [
                            'admin' => null,
                            'editor' => null,
                            'api' => null,
                        ],
                    ]
                )
            );

        $this->policyProvider->addPolicies($this->configBuilderMock);
    }
}
