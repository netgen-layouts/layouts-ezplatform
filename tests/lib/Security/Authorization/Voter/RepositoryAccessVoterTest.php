<?php

namespace Netgen\BlockManager\Ez\Tests\Security\Authorization\Voter;

use eZ\Publish\API\Repository\Repository;
use Netgen\BlockManager\Ez\Security\Authorization\Voter\RepositoryAccessVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class RepositoryAccessVoterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repositoryMock;

    /**
     * @var \Netgen\BlockManager\Ez\Security\Authorization\Voter\RepositoryAccessVoter
     */
    protected $voter;

    public function setUp()
    {
        $this->repositoryMock = $this->createMock(Repository::class);

        $this->voter = new RepositoryAccessVoter($this->repositoryMock);
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
        $i = 0;
        foreach ($repoAccess as $function => $hasAccess) {
            $this->repositoryMock
                ->expects($this->at($i++))
                ->method('hasAccess')
                ->with($this->equalTo('nglayouts'), $this->equalTo($function))
                ->will($this->returnValue($hasAccess));
        }

        $result = $this->voter->vote(
            $this->createMock(TokenInterface::class),
            null,
            array($attribute)
        );

        $this->assertEquals($voteResult, $result);
    }

    public function voteDataProvider()
    {
        return array(
            // Only matches admin eZ function
            array('ngbm:admin', array('admin' => true), VoterInterface::ACCESS_GRANTED),
            array('ngbm:admin', array('admin' => false), VoterInterface::ACCESS_DENIED),

            // Matches both admin and editor eZ functions
            array('ngbm:editor', array('editor' => true), VoterInterface::ACCESS_GRANTED),
            array('ngbm:editor', array('editor' => false, 'admin' => true), VoterInterface::ACCESS_GRANTED),
            array('ngbm:editor', array('editor' => false, 'admin' => false), VoterInterface::ACCESS_DENIED),

            array('ngbm:unknown', array(), VoterInterface::ACCESS_ABSTAIN),
        );
    }
}
