<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaBundle\Tests\Security;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigBuilderInterface;
use Netgen\Bundle\LayoutsIbexaBundle\Security\PolicyProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class PolicyProviderTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigBuilderInterface
     */
    private MockObject $configBuilderMock;

    private PolicyProvider $policyProvider;

    protected function setUp(): void
    {
        $this->configBuilderMock = $this->createMock(ConfigBuilderInterface::class);

        $this->policyProvider = new PolicyProvider();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Security\PolicyProvider::addPolicies
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
                    ],
                ),
            );

        $this->policyProvider->addPolicies($this->configBuilderMock);
    }
}
