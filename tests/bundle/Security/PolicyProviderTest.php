<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Security;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigBuilderInterface;
use Netgen\Bundle\EzPublishBlockManagerBundle\Security\PolicyProvider;
use PHPUnit\Framework\TestCase;

final class PolicyProviderTest extends TestCase
{
    /**
     * @var \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigBuilderInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $configBuilderMock;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Security\PolicyProvider
     */
    private $policyProvider;

    public function setUp(): void
    {
        $this->configBuilderMock = $this->createMock(ConfigBuilderInterface::class);

        $this->policyProvider = new PolicyProvider();
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Security\PolicyProvider::addPolicies
     */
    public function testAddPolicies(): void
    {
        $this->configBuilderMock
            ->expects(self::once())
            ->method('addConfig')
            ->with(
                self::identicalTo(
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
