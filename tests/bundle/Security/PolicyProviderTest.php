<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Tests\Security;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigBuilderInterface;
use Netgen\Bundle\LayoutsEzPlatformBundle\Security\PolicyProvider;
use PHPUnit\Framework\TestCase;

final class PolicyProviderTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigBuilderInterface
     */
    private $configBuilderMock;

    /**
     * @var \Netgen\Bundle\LayoutsEzPlatformBundle\Security\PolicyProvider
     */
    private $policyProvider;

    protected function setUp(): void
    {
        $this->configBuilderMock = $this->createMock(ConfigBuilderInterface::class);

        $this->policyProvider = new PolicyProvider();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\Security\PolicyProvider::addPolicies
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
