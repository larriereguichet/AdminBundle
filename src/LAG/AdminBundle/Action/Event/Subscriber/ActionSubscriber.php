<?php
//
//namespace LAG\AdminBundle\Action\Event\Subscriber;
//
//use LAG\AdminBundle\Action\AdminAction;
//use Symfony\Component\EventDispatcher\EventSubscriberInterface;
//use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
//use Symfony\Component\HttpKernel\KernelEvents;
//use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
//use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
//use Symfony\Component\Security\Core\User\UserInterface;
//
//class ActionSubscriber implements EventSubscriberInterface
//{
//    /**
//     * @var TokenStorage
//     */
//    private $tokenStorage;
//    /**
//     * @var AuthorizationChecker
//     */
//    private $authorizationChecker;
//
//    public function __construct(TokenStorage $tokenStorage, AuthorizationChecker $authorizationChecker)
//    {
//        $this->tokenStorage = $tokenStorage;
//        $this->authorizationChecker = $authorizationChecker;
//    }
//
//    public static function getSubscribedEvents()
//    {
//        return [
//            KernelEvents::REQUEST => 'onKernelRequest',
//        ];
//    }
//
//    public function onKernelRequest(FilterControllerEvent $event)
//    {
//        $controller = $event->getController();
//
//        if (!is_array($controller)) {
//            return;
//        }
//
//        if ($controller[0] instanceof AdminAction) {
//            $user = $this
//                ->tokenStorage
//                ->getToken()
//                ->getUser();
//
//            if (!$user instanceof UserInterface) {
//                return;
//            }
//            $this
//                ->authorizationChecker
//                ->isGranted()
//        }
//    }
//
//    protected function getUser()
//    {
//        return
//    }
//}
