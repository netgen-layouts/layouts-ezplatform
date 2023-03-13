<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaBundle\Tests\Security;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigBuilderInterface;
use Netgen\Bundle\LayoutsIbexaBundle\Security\PolicyProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(PolicyProvider::class)]
final class PolicyProviderTest extends TestCase
{
    private MockObject&ConfigBuilderInterface $configBuilderMock;

    private PolicyProvider $policyProvider;

    protected function setUp(): void
    {
        $this->configBuilderMock = $this->createMock(ConfigBuilderInterface::class);

        $this->policyProvider = new PolicyProvider();
    }

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
