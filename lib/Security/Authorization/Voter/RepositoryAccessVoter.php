<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Security\Authorization\Voter;

use Ibexa\Core\MVC\Symfony\Security\Authorization\Attribute;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

use function str_starts_with;

/**
 * Votes on Netgen Layouts attributes (ROLE_NGLAYOUTS_*) by matching corresponding access
 * rights in Ibexa CMS Repository.
 */
final class RepositoryAccessVoter extends Voter
{
    /**
     * Identifier of the Ibexa CMS module used for creating Netgen Layouts permissions.
     */
    private const MODULE = 'nglayouts';

    /**
     * Map of supported attributes to corresponding functions in the Ibexa CMS module.
     */
    private const ATTRIBUTE_TO_POLICY_MAP = [
        'ROLE_NGLAYOUTS_ADMIN' => 'admin',
        'ROLE_NGLAYOUTS_EDITOR' => 'editor',
        'ROLE_NGLAYOUTS_API' => 'api',
    ];

    public function __construct(
        private RoleHierarchyInterface $roleHierarchy,
        private AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    /**
     * @param mixed[] $attributes
     */
    public function vote(TokenInterface $token, mixed $subject, array $attributes): int
    {
        // abstain vote by default in case none of the attributes are supported
        $vote = self::ACCESS_ABSTAIN;

        foreach ($attributes as $attribute) {
            if (!$this->supports($attribute, $subject)) {
                continue;
            }

            $reachableAttributes = $this->roleHierarchy->getReachableRoleNames([$attribute]);

            // rely on vote resolved by parent implementation
            $vote = parent::vote($token, $subject, $reachableAttributes);

            // return only if granted
            if ($vote === self::ACCESS_GRANTED) {
                return self::ACCESS_GRANTED;
            }
        }

        return $vote;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return str_starts_with($attribute, 'ROLE_NGLAYOUTS_');
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
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
}
