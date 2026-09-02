# Events

Events are the way to add application logic without touching controllers. They are dispatched
by `ResourceEventDispatcher`, which fires **the same event object three times** under three
increasingly specific names.

## The three names

For a resource `article` in application `admin`, the controller event is dispatched as:

```
lag_admin.resource.controller     ← every resource, every application
admin.resource.controller         ← every resource of the admin application
admin.article.controller          ← this resource only
```

So a listener on `lag_admin.resource.controller` sees everything, and a listener on
`admin.article.controller` sees one resource. Listen to whichever level fits.

> Because the same object is dispatched three times, a listener that mutates it must be
> idempotent, and a listener registered on two of the three names runs twice.

## Event catalogue

| Constant | Generic name | Object | Dispatched |
|---|---|---|---|
| `ResourceControllerEvents::RESOURCE_CONTROLLER` | `{application}.{resource}.controller` | `ResourceControllerEvent` | in every controller, after the data is provided and before the response is built |
| `DataEvents::DATA_PROCESS` | `lag_admin.resource.data_process` | `DataEvent` | before the processor runs |
| `DataEvents::DATA_PROCESSED` | `lag_admin.resource.data_processed` | `DataEvent` | after the processor ran |
| `OperationEvents::OPERATION_CREATE` / `OPERATION_CREATED` | `lag_admin.resource.operation_create(d)` | `OperationEvent` | while operation metadata is built |
| `GridEvents::GRID_EVENT` | `lag_admin.resource.grid` | `GridEvent` | while a grid is built |
| `FilterEvents::FILTER_CREATE` / `FILTER_CREATED` | `lag_admin.resource.filter_create(d)` | `FilterEvent` | while filters are built |

## Reacting to a controller

`ResourceControllerEvent` carries the operation, the request and the data — and it can
short-circuit the response:

```php
namespace App\EventListener;

use LAG\AdminBundle\Event\ResourceControllerEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[AsEventListener(event: 'admin.article.controller')]
final readonly class RedirectDraftListener
{
    public function __invoke(ResourceControllerEvent $event): void
    {
        if ($event->getOperation()->getShortName() !== 'show') {
            return;
        }

        if ($event->getData()?->isDraft()) {
            $event->setResponse(new RedirectResponse('/'));
        }
    }
}
```

When a listener sets a response, the controller returns it as-is: no template is rendered.

## Reacting to writes

`DataEvent` is dispatched around the processor by `EventProcessor`:

```php
#[AsEventListener(event: 'admin.article.data_processed')]
final readonly class NotifyOnPublishListener
{
    public function __invoke(DataEvent $event): void
    {
        $article = $event->getData();
        // …
    }
}
```

The bundle uses this hook itself, on `lag_admin.resource.data_process` at priority 250:

| Listener | Does |
|---|---|
| `GenerateTimestampListener` | fills created/updated timestamps |
| `GenerateSlugListener` | fills every `Slug` property from its source |
| `UploadImageListener` | uploads images of `ImageAwareInterface` / `ImagesAwareInterface` data |
| `GeneratePasswordListener` | hashes the plain password of a `PasswordAuthenticatedResourceInterface` |

## Kernel listeners

Two listeners run on `kernel.request`:

* `InitializeResourceContextListener` (priority `-255`) reads `_lag_operation` and populates the
  resource context, which is what the Twig global, the menus and the value resolvers read;
* `AccessListener` denies access when the operation's permissions are not granted.

## Choosing between an event and a decorator

| You want to… | Use |
|---|---|
| change *what data is read* | a provider, or a provider decorator |
| change *what happens on write* | a processor, or a `data_process` listener |
| return a different response | a `ResourceControllerEvent` listener |
| add a cross-cutting concern to every operation | a decorator |
| react to one resource only | an event listener on `{application}.{resource}.…` |

See [Custom state](../customization/state.md).

## Next

[Translations](translations.md).
