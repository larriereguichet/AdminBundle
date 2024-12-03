<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Security\Voter;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/** @extends Voter<string, OperationInterface> */
final class OperationPermissionVoter extends Voter
{
    public const string RESOURCE_ACCESS = 'resource_access';

    public function __construct(
        private readonly Security $security,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof OperationInterface && $attribute === self::RESOURCE_ACCESS;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        \assert($subject instanceof OperationInterface);

        foreach ($subject->getPermissions() as $permission) {
            if ($this->security->isGranted($permission, $token->getUser())) {
                return true;
            }
        }

        return \count($subject->getPermissions()) === 0;
    }
}
