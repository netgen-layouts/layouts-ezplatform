<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Security\Authorization\Voter;

use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use Netgen\Layouts\Ez\Security\Authorization\Voter\RepositoryAccessVoter;
use Netgen\Layouts\Ez\Security\Role\RoleHierarchy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class RepositoryAccessVoterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $accessDecisionManagerMock;

    /**
     * @var \Netgen\Layouts\Ez\Security\Authorization\Voter\RepositoryAccessVoter
     */
    private $voter;

    protected function setUp(): void
    {
        $roleHierarchy = new RoleHierarchy(
            [
                'ROLE_NGLAYOUTS_ADMIN' => [
                    'ROLE_NGLAYOUTS_EDITOR',
                ],
            ]
        );

        $this->accessDecisionManagerMock = $this->createMock(AccessDecisionManagerInterface::class);

        $this->voter = new RepositoryAccessVoter(
            $roleHierarchy,
            $this->accessDecisionManagerMock
        );
    }

    /**
     * @param array<string, bool> $repoAccess
     *
     * @covers \Netgen\Layouts\Ez\Security\Authorization\Voter\RepositoryAccessVoter::__construct
     * @covers \Netgen\Layouts\Ez\Security\Authorization\Voter\RepositoryAccessVoter::getReachableAttributes
     * @covers \Netgen\Layouts\Ez\Security\Authorization\Voter\RepositoryAccessVoter::supports
     * @covers \Netgen\Layouts\Ez\Security\Authorization\Voter\RepositoryAccessVoter::vote
     * @covers \Netgen\Layouts\Ez\Security\Authorization\Voter\RepositoryAccessVoter::voteOnAttribute
     *
     * @dataProvider voteDataProvider
     */
    public function testVote(string $attribute, array $repoAccess, int $voteResult): void
    {
        $token = $this->createMock(TokenInterface::class);

        $i = 0;
        foreach ($repoAccess as $function => $hasAccess) {
            $this->accessDecisionManagerMock
                ->expects(self::at($i++))
                ->method('decide')
                ->with(
                    self::identicalTo($token),
                    self::equalTo([new Attribute('nglayouts', $function)]),
                    self::isNull()
                )
                ->willReturn($hasAccess);
        }

        $result = $this->voter->vote($token, null, [$attribute]);

        self::assertSame($voteResult, $result);
    }

    public function voteDataProvider(): array
    {
        return [
            // Only matches admin eZ function
            ['ROLE_NGLAYOUTS_ADMIN', ['admin' => true], VoterInterface::ACCESS_GRANTED],
            ['ROLE_NGLAYOUTS_ADMIN', ['admin' => false], VoterInterface::ACCESS_DENIED],

            // Matches both admin and editor eZ functions
            ['ROLE_NGLAYOUTS_EDITOR', ['editor' => true], VoterInterface::ACCESS_GRANTED],
            ['ROLE_NGLAYOUTS_EDITOR', ['editor' => false, 'admin' => true], VoterInterface::ACCESS_GRANTED],
            ['ROLE_NGLAYOUTS_EDITOR', ['editor' => false, 'admin' => false], VoterInterface::ACCESS_DENIED],

            ['ROLE_NGLAYOUTS_UNKNOWN', [], VoterInterface::ACCESS_DENIED],

            ['ROLE_UNSUPPORTED', [], VoterInterface::ACCESS_ABSTAIN],
        ];
    }
}
