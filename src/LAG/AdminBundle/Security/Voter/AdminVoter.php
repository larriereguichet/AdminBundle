<?php

namespace LAG\AdminBundle\Security\Voter;

use LAG\AdminBundle\Admin\Request\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminVoter extends Voter
{
    /**
     * @var RequestHandlerInterface
     */
    private $requestHandler;
    
    /**
     * @var RequestStack
     */
    private $requestStack;
    
    /**
     * AdminVoter constructor.
     *
     * @param RequestHandlerInterface $requestHandler
     * @param RequestStack $requestStack
     */
    public function __construct(
        RequestHandlerInterface $requestHandler,
        RequestStack $requestStack
    ) {
        $this->requestHandler = $requestHandler;
        $this->requestStack = $requestStack;
    }
    
    /**
     * @param string $attribute
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        if (!$subject instanceof UserInterface) {
            return false;
        }
        $request = $this
            ->requestStack
            ->getCurrentRequest()
        ;
    
        if (!$this->requestHandler->supports($request)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $request = $this
            ->requestStack
            ->getCurrentRequest()
        ;
        $admin = $this
            ->requestHandler
            ->handle($request)
        ;
        $roles = $admin
            ->getConfiguration()
            ->getParameter('permissions')
        ;
    
        if (!in_array($attribute, $roles)) {
            return false;
        }
        
        return true;
    }
}
