<?php

namespace Netgen\BlockManager\Ez\Tests\Security\Authorization\Voter;

use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use Netgen\BlockManager\Ez\Security\Authorization\Voter\RepositoryAccessVoter;
use Netgen\BlockManager\Ez\Security\Role\RoleHierarchy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class RepositoryAccessVoterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $accessDecisionManagerMock;

    /**
     * @var \Netgen\BlockManager\Ez\Security\Authorization\Voter\RepositoryAccessVoter
     */
    protected $voter;

    public function setUp()
    {
        $roleHierarchy = new RoleHierarchy(
            array(
                'ROLE_NGBM_ADMIN' => array(
                    'ROLE_NGBM_EDITOR',
                ),
            )
        );

        $this->accessDecisionManagerMock = $this->createMock(AccessDecisionManagerInterface::class);

        $this->voter = new RepositoryAccessVoter(
            $roleHierarchy,
            $this->accessDecisionManagerMock
        );
    }

    /**
     * @param string $attribute
     * @param array $repoAccess
     * @param int $voteResult
     *
     * @covers \Netgen\BlockManager\Ez\Security\Authorization\Voter\RepositoryAccessVoter::__construct
     * @covers \Netgen\BlockManager\Ez\Security\Authorization\Voter\RepositoryAccessVoter::vote
     * @covers \Netgen\BlockManager\Ez\Security\Authorization\Voter\RepositoryAccessVoter::supports
     * @covers \Netgen\BlockManager\Ez\Security\Authorization\Voter\RepositoryAccessVoter::voteOnAttribute
     * @covers \Netgen\BlockManager\Ez\Security\Authorization\Voter\RepositoryAccessVoter::getReachableAttributes
     *
     * @dataProvider voteDataProvider
     */
    public function testVote($attribute, array $repoAccess, $voteResult)
    {
        $token = $this->createMock(TokenInterface::class);

        $i = 0;
        foreach ($repoAccess as $function => $hasAccess) {
            $this->accessDecisionManagerMock
                ->expects($this->at($i++))
                ->method('decide')
                ->with(
                    $this->equalTo($token),
                    $this->equalTo(array(new Attribute('nglayouts', $function))),
                    $this->isNull()
                )
                ->will($this->returnValue($hasAccess));
        }

        $result = $this->voter->vote($token, null, array($attribute));

        $this->assertEquals($voteResult, $result);
    }

    public function voteDataProvider()
    {
        return array(
            // Only matches admin eZ function
            array('ROLE_NGBM_ADMIN', array('admin' => true), VoterInterface::ACCESS_GRANTED),
            array('ROLE_NGBM_ADMIN', array('admin' => false), VoterInterface::ACCESS_DENIED),

            // Matches both admin and editor eZ functions
            array('ROLE_NGBM_EDITOR', array('editor' => true), VoterInterface::ACCESS_GRANTED),
            array('ROLE_NGBM_EDITOR', array('editor' => false, 'admin' => true), VoterInterface::ACCESS_GRANTED),
            array('ROLE_NGBM_EDITOR', array('editor' => false, 'admin' => false), VoterInterface::ACCESS_DENIED),

            array('ROLE_NGBM_UNKNOWN', array(), VoterInterface::ACCESS_DENIED),

            array('ROLE_UNSUPPORTED', array(), VoterInterface::ACCESS_ABSTAIN),
        );
    }
}
