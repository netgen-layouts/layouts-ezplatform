<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Security\Authorization\Voter;

use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\Role as SymfonyRole;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

use function array_map;
use function is_string;
use function str_starts_with;

/**
 * Votes on Netgen Layouts attributes (ROLE_NGLAYOUTS_*) by matching corresponding access
 * rights in eZ Platform Repository.
 *
 * @extends \Symfony\Component\Security\Core\Authorization\Voter\Voter<string, \Netgen\Layouts\API\Values\Value|null>
 */
final class RepositoryAccessVoter extends Voter
{
    /**
     * Identifier of the eZ Platform module used for creating Netgen Layouts permissions.
     */
    private const MODULE = 'nglayouts';

    /**
     * Map of supported attributes to corresponding functions in the eZ Platform module.
     */
    private const ATTRIBUTE_TO_POLICY_MAP = [
        'ROLE_NGLAYOUTS_ADMIN' => 'admin',
        'ROLE_NGLAYOUTS_EDITOR' => 'editor',
        'ROLE_NGLAYOUTS_API' => 'api',
    ];

    private RoleHierarchyInterface $roleHierarchy;

    private AccessDecisionManagerInterface $accessDecisionManager;

    public function __construct(
        RoleHierarchyInterface $roleHierarchy,
        AccessDecisionManagerInterface $accessDecisionManager
    ) {
        $this->roleHierarchy = $roleHierarchy;
        $this->accessDecisionManager = $accessDecisionManager;
    }

    /**
     * @param mixed $subject
     * @param mixed[] $attributes
     */
    public function vote(TokenInterface $token, $subject, array $attributes): int
    {
        // abstain vote by default in case none of the attributes are supported
        $vote = self::ACCESS_ABSTAIN;

        foreach ($attributes as $attribute) {
            if (!$this->supports($attribute, $subject)) {
                continue;
            }

            $reachableAttributes = $this->getReachableAttributes($attribute);

            // rely on vote resolved by parent implementation
            $vote = parent::vote($token, $subject, $reachableAttributes);

            // return only if granted
            if ($vote === self::ACCESS_GRANTED) {
                return self::ACCESS_GRANTED;
            }
        }

        return $vote;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     */
    protected function supports($attribute, $subject): bool
    {
        return is_string($attribute) && str_starts_with($attribute, 'ROLE_NGLAYOUTS_');
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        if (!isset(self::ATTRIBUTE_TO_POLICY_MAP[$attribute])) {
            return false;
        }

        $function = self::ATTRIBUTE_TO_POLICY_MAP[$attribute];

        return $this->accessDecisionManager->decide(
            $token,
            [new Attribute(self::MODULE, $function)],
            $subject,
        );
    }

    /**
     * Return all attributes reachable by the given $attribute through hierarchy.
     *
     * @param string $attribute
     *
     * @return string[]
     */
    private function getReachableAttributes($attribute): array
    {
        if (Kernel::VERSION_ID >= 40400) {
            return $this->roleHierarchy->getReachableRoleNames([$attribute]);
        }

        return array_map(
            static fn (SymfonyRole $role): string => $role->getRole(),
            $this->roleHierarchy->getReachableRoles([new Role($attribute)]),
        );
    }
}
