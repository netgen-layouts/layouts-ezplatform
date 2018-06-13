<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Security\Authorization\Voter;

use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

/**
 * Votes on Netgen Layouts attributes (ROLE_NGBM_*) by matching corresponding access
 * rights in eZ Platform Repository.
 */
final class RepositoryAccessVoter extends Voter
{
    /**
     * Identifier of the eZ Publish module used for creating Netgen Layouts permissions.
     *
     * @var string
     */
    private static $module = 'nglayouts';

    /**
     * Map of supported attributes to corresponding functions in the eZ Publish module.
     *
     * @var array
     */
    private static $attributeToPolicyMap = [
        'ROLE_NGBM_ADMIN' => 'admin',
        'ROLE_NGBM_EDITOR' => 'editor',
        'ROLE_NGBM_API' => 'api',
    ];

    /**
     * @var \Symfony\Component\Security\Core\Role\RoleHierarchyInterface
     */
    private $roleHierarchy;

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface
     */
    private $accessDecisionManager;

    public function __construct(
        RoleHierarchyInterface $roleHierarchy,
        AccessDecisionManagerInterface $accessDecisionManager
    ) {
        $this->roleHierarchy = $roleHierarchy;
        $this->accessDecisionManager = $accessDecisionManager;
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        // abstain vote by default in case none of the attributes are supported
        $vote = self::ACCESS_ABSTAIN;

        foreach ($attributes as $attribute) {
            if (!$this->supports($attribute, $object)) {
                continue;
            }

            $reachableAttributes = $this->getReachableAttributes($attribute);

            // rely on vote resolved by parent implementation
            $vote = parent::vote($token, $object, $reachableAttributes);

            // return only if granted
            if ($vote === self::ACCESS_GRANTED) {
                return self::ACCESS_GRANTED;
            }
        }

        return $vote;
    }

    protected function supports($attribute, $subject)
    {
        return is_string($attribute) && mb_strpos($attribute, 'ROLE_NGBM_') === 0;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if (!isset(self::$attributeToPolicyMap[$attribute])) {
            return false;
        }

        $function = self::$attributeToPolicyMap[$attribute];

        return $this->accessDecisionManager->decide(
            $token,
            [new Attribute(self::$module, $function)],
            $subject
        );
    }

    /**
     * Return all attributes reachable by the given $attribute through hierarchy.
     *
     * @param string $attribute
     *
     * @return string[]
     */
    private function getReachableAttributes($attribute)
    {
        return array_map(
            function (Role $role) {
                return $role->getRole();
            },
            $this->roleHierarchy->getReachableRoles([new Role($attribute)])
        );
    }
}
