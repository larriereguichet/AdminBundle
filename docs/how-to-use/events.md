# Events

Like **Symfony**, The `AdminBundle` use events to gather data in order to build the **Admin view** can be customized 
using events. Here is a list of all available events to help you to finely customize your `Admin`. The list is available
 in the `LAG\AdminBundle\Event\Events` class.
 
## ADMIN_HANDLE_REQUEST

This event is dispatched when an `Admin` route is called. It is responsible for build the current `Action` (list, edit...),
and to set this `Action` in the event. It also in charge of checking if the current **User** is allowed to see a view or
to do an action.

You can set an `Action` to this event, and get the current `Admin` object. 

### Changing the current Action class
You can change the current `Action` class using the `ADMIN_HANDLE_REQUEST` event :

```php
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use LAG\AdminBundle\Event\EventsOLD;
use LAG\AdminBundle\Event\EventsOLD\AdminEvent;


class MySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            Events::ADMIN_HANDLE_REQUEST => 'handleRequest',
        ];
    }
    
    public function handleRequest(AdminEvent $event): void
    {
        // Get the current Admin object
        $admin = $event->getAdmin();
        
        // For instance, create an action according to the Admin name
        if ($admin->getName() === 'my_custom_name') {
            // Create a new Action object implementing LAG\AdminBundle\Admin\ActionInterface
            // $action = ...     
        }
        $event->setAction($action);
        
        // Latter in the code (when this event dispatched), you can use $admin->getCurrentAction() and your Action object will be returned
    }
}
```

### Denying access for the current user
The `AdminBundle` use **Symfony** roles to check permissions. It can be configured the `Admin` configuration or for each
`Action`, using the following configuration :

```yaml

my_admin:
    # This permissions are checked for each Actions in the Admin
    permissions: [ROLE_MY_ADMIN_ROLE]
    actions:
        list:
            # Only users with this role can see the list of entities
            permissions: [ROLE_MY_ACTION_ROLE]
```

> By convention, roles in **Symfony** starts with `ROLE_`.

You can also deny access using your custom code to check permissions. Here is an example :

```php
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;use LAG\AdminBundle\Event\EventsOLD;use LAG\AdminBundle\Event\EventsOLD\AdminEvent;use Symfony\Component\EventDispatcher\EventSubscriberInterface;use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MySubscriber implements EventSubscriberInterface
{
    /**
     * @var \LAG\AdminBundle\Admin\Configuration\ApplicationConfiguration
     */
    private $applicationConfiguration;
    
    public static function getSubscribedEvents()
    {
        return [
            Events::ADMIN_HANDLE_REQUEST => 'handleRequest',
        ];
    }

    public function __construct(ApplicationConfigurationStorage $applicationConfigurationStorage)
    {        
        $this->applicationConfiguration = $applicationConfigurationStorage->getConfiguration();
    }
    
    public function handleRequest(AdminEvent $event)
    {
        // Check if the security is enabled in the global Admin Application configuration
        if (!$this->applicationConfiguration->getParameter('enable_security')) {
            return;
        }
        $configuration = $event->getAdmin()->getConfiguration();
        $roles = $configuration->getParameter('permissions');
            
        // Do something to check if the user is allowed. If not, throw an AccessDeniedException        
        if (!$allowed) {
            throw new AccessDeniedException();
        }
    }
}
```

## ADMIN_FILTER
The `ADMIN_FILTER` event is in charge of creating filters according to the configuration. Those filters will be applied
to the query builder (when using Doctrine ORM). A form is built with the filters to be displayed.

> Filters are only relevant in a `list` action. When only entity or no entity is loaded, the identifier will be used to
> load the entity.

### Override the filter form
Filters can be configured using the `Admin` yaml configuration. See the [Filters](filters.md) documentation for more 
information. Sometimes, you may want to override the whole filter form to use your custom form. This can be achieved 
using the `ADMIN_FILTER` event.

```php
use LAG\AdminBundle\Event\EventsOLD;
use LAG\AdminBundle\Event\EventsOLD\FilterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FilterSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            Events::ADMIN_FILTER => 'onFilter',
        ];
    }

    public function onFilter(FilterEvent $event): void
    {
        // Remove the existing filter form
        $event->removeForm('filter');
           
        // Add new custom filter form. The removeForm() method should be called before setting a new custom form
        $event->addForm($myCustomForm, 'filter');
    }
}
```

### Add a custom Filter object
Usually the filter form is built from the configuration. When the yaml configuration is not sufficient, you can add 
your custom filter object or remove existing filters.

> Filters object should implement `LAG\AdminBundle\Filter\FilterInterface`.

```php
use LAG\AdminBundle\Event\EventsOLD;
use LAG\AdminBundle\Event\EventsOLD\FilterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FilterSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            Events::ADMIN_FILTER => 'onFilter',
        ];
    }

    public function onFilter(FilterEvent $event): void
    {
        // Get existing filters
        $filters = $event->getFilters();
        
        // Add your custom filter
        $event->addFilter($myCustomFilter);

        // Or remove any existing filters
        $event->clearFilters();
    }
}
```

## ENTITY_LOAD
This event is in charge of entity loading from the database. For the `list` action, several entities should be loaded. For
the `edit` and `create` action, only one entity is loaded.

> For more information about loading strategy, see the [Loading Strategy](../reference/loading-strategy.md) documentation.

### Load entities with a custom code
The `AdminBundle` load entities using data providers (see the [Data Provider](data-provider.md)) documentation for more
information about data providers. But you can load entities using your custom code.

```php
use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Event\EventsOLD;
use LAG\AdminBundle\Event\EventsOLD\EntityEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdminSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            // Use a priority higher than 255
            Events::ENTITY_LOAD => [
                'loadEntities', 300,
            ],            
        ];
    }
    
    public function loadEntities(EntityEvent $event): void
    {
        // Load your entities
        // ...
        
        // Set the event entities which will be passed to the Admin
        $event->setEntities(new ArrayCollection($myEntities));
    }
}
```

## ADMIN_CREATE_FORM

Documentation in progress...

## ADMIN_FILTER

Documentation in progress...

## ADMIN_VIEW

Documentation in progress...

Previous: [Admin](field.md)

Next: [Field](data-provider.md)
