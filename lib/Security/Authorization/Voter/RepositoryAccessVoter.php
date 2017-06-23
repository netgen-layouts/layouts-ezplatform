<?php

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
class RepositoryAccessVoter extends Voter
{
    /**
     * Identifier of the Legacy Stack module used for creating Netgen Layouts permissions.
     *
     * @var string
     */
    protected static $module = 'nglayouts';

    /**
     * Map of supported attributes to corresponding functions in the Legacy Stack module.
     *
     * @var array
     */
    protected static $attributeToPolicyMap = array(
        'ROLE_NGBM_ADMIN' => 'admin',
        'ROLE_NGBM_EDITOR' => 'editor',
        'ROLE_NGBM_API' => 'api',
    );

    /**
     * @var \Symfony\Component\Security\Core\Role\RoleHierarchyInterface
     */
    protected $roleHierarchy;

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface
     */
    protected $accessDecisionManager;

    /**
     * @param \Symfony\Component\Security\Core\Role\RoleHierarchyInterface $roleHierarchy
     * @param \Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface $accessDecisionManager
     */
    public function __construct(
        RoleHierarchyInterface $roleHierarchy,
        AccessDecisionManagerInterface $accessDecisionManager
    ) {
        $this->roleHierarchy = $roleHierarchy;
        $this->accessDecisionManager = $accessDecisionManager;
    }

    /**
     * Returns the vote for the given parameters.
     *
     * This method must return one of the following constants:
     * ACCESS_GRANTED, ACCESS_DENIED, or ACCESS_ABSTAIN.
     *
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param object|null $object
     * @param array $attributes
     *
     * @return int either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
     */
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

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        return is_string($attribute) && strpos($attribute, 'ROLE_NGBM_') === 0;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string $attribute
     * @param mixed $subject
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if (!isset(self::$attributeToPolicyMap[$attribute])) {
            return false;
        }

        $function = self::$attributeToPolicyMap[$attribute];

        return $this->accessDecisionManager->decide(
            $token,
            array(new Attribute(self::$module, $function)),
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
    protected function getReachableAttributes($attribute)
    {
        $reachableRoles = $this->roleHierarchy->getReachableRoles(array(new Role($attribute)));

        $reachableAttributes = array();
        foreach ($reachableRoles as $reachableRole) {
            $reachableAttributes[] = $reachableRole->getRole();
        }

        return $reachableAttributes;
    }
}
