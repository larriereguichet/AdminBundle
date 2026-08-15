<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Security\Voter;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/** @extends Voter<string, OperationInterface> */
final class OperationVoter extends Voter
{
    public const string OPERATION_ACCESS = 'resource_access';

    public function __construct(
        private readonly Security $security,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof OperationInterface && $attribute === self::OPERATION_ACCESS;
    }

    /** @param OperationInterface $subject */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $permissions = $subject->getRoles() ?? [];

        // When no roles are defined, allow user
        if ($permissions === []) {
            return true;
        }

        // User must have at least one of the configured roles
        foreach ($subject->getRoles() as $permission) {
            if ($this->security->isGranted($permission, $token->getUser())) {
                return true;
            }
        }

        return false;
    }
}
