<?php /** @noinspection PhpDocSignatureInspection */


namespace App\EventSubscriber;


use ApiPlatform\Symfony\EventListener\EventPriorities as EventPrioritiesAlias;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class UserMeSubscriber implements EventSubscriberInterface
{
    /**
     * @param Security $security
     */
    public function __construct(private Security $security)
    {
    }

    /**
     * @return array<string, array<int, int|string>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => ['resolveMe', EventPrioritiesAlias::PRE_READ],
        ];
    }

    /**
     * @param RequestEvent $event
     */
    public function resolveMe(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ('api_users_me_collection' !== $request->attributes->get('_route')) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        $request->attributes->set('id', $user->getId());
        $request->attributes->set('_route', 'api_users_get_item');
        $request->attributes->set('_controller', 'api_platform.action.get_item');
        $request->attributes->set('_api_collection_operation_name', '');
        $request->attributes->set('_api_item_operation_name', 'get');
    }
}