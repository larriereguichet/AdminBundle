<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Security\Voter;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OperationPermissionVoter extends Voter
{
    public const RESOURCE_ACCESS = 'resource_access';

    public function __construct(
        private Security $security,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof OperationInterface && $attribute === self::RESOURCE_ACCESS;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        \assert($subject instanceof OperationInterface);

        if (\count($subject->getPermissions()) > 0 && $token->getUser() === null) {
            return false;
        }

        foreach ($subject->getPermissions() as $permission) {
            if (!$this->security->isGranted($permission, $token->getUser())) {
                return false;
            }
        }

        return true;
    }
}
