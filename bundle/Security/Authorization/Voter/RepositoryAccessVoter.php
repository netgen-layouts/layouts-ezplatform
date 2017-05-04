<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Security\Authorization\Voter;

use eZ\Publish\API\Repository\Repository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RepositoryAccessVoter extends Voter
{
    /**
     * Identifier of the Legacy Stack module used for creating Block Manager permissions.
     *
     * @var string
     */
    private static $module = 'ngblockmanager';

    /**
     * Map of supported attributes to corresponding functions in the Legacy Stack module.
     *
     * @var array
     */
    private static $attributeToPolicyMap = array(
        'ngbm:admin' => 'admin',
        'ngbm:editor' => 'editor',
    );

    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    private $repository;

    /**
     * @param \eZ\Publish\API\Repository\Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    protected function supports($attribute, $subject)
    {
        return is_string($attribute) && isset(self::$attributeToPolicyMap[$attribute]);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $function = self::$attributeToPolicyMap[$attribute];

        return $this->repository->hasAccess(self::$module, $function);
    }
}
