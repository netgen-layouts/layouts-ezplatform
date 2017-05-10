<?php

namespace Netgen\BlockManager\Ez\Security\Authorization\Voter;

use eZ\Publish\API\Repository\Repository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Votes on Netgen Layouts attributes (ngbm:*) by matching corresponding access
 * rights in eZ Platform Repository.
 */
class RepositoryAccessVoter extends Voter
{
    /**
     * Identifier of the Legacy Stack module used for creating Netgen Layouts permissions.
     *
     * @var string
     */
    private static $module = 'nglayouts';

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
     * Describes inverted attribute hierarchy.
     *
     * Only one level, explicit (no indirection is allowed).
     *
     * @var array
     */
    private static $attributeHierarchy = array(
        'ngbm:editor' => array(
            'ngbm:admin',
        ),
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
        return is_string($attribute) && isset(self::$attributeToPolicyMap[$attribute]);
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
        $function = self::$attributeToPolicyMap[$attribute];

        return $this->repository->hasAccess(self::$module, $function);
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
        $attributes = array($attribute);

        if (isset(self::$attributeHierarchy[$attribute])) {
            $attributes = array_merge($attributes, self::$attributeHierarchy[$attribute]);
        }

        return $attributes;
    }
}
