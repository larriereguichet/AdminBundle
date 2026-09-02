# Workflow

With `symfony/workflow` installed, an operation can apply a transition and a link can be shown
only when the transition is allowed. The integration is removed from the container when the
component is absent, so nothing breaks without it.

## Declare the workflow

Standard Symfony configuration:

```yaml
# config/packages/workflow.yaml
framework:
    workflows:
        order:
            type: state_machine
            marking_store:
                type: method
                property: state
            supports:
                - App\Entity\Order
            initial_marking: new
            places: [new, prepared, delivered, paid, cancelled]
            transitions:
                prepare:
                    from: new
                    to: prepared
                deliver:
                    from: prepared
                    to: delivered
                pay:
                    from: delivered
                    to: paid
                cancel:
                    from: [new, prepared]
                    to: cancelled
```

## An operation that applies a transition

```php
new Update(
    name: 'prepare',
    title: 'order.prepare',
    path: '/{number}/prepare',
    form: FormType::class,
    formOptions: ['label' => false],
    workflow: 'order',
    workflowTransition: 'prepare',
    flashMessage: 'order.prepared',
)
```

`WorkflowProcessor` applies the transition **before** delegating to the rest of the processor
chain, so the record is persisted in its new state. The transition is skipped silently when the
workflow does not support the data.

A `Create` can apply an initial transition the same way:

```php
new Create(workflow: 'order', workflowTransition: 'create')
```

## Links that follow the state machine

The natural pairing: one operation per transition, one link per operation, each shown only when
the transition is currently possible.

```php
new Index(
    grid: 'admin_orders',
    itemLinks: [
        new Link(
            operation: 'prepare',
            text: 'order.prepare',
            icon: 'bi:basket',
            attributes: ['class' => 'btn btn-primary'],
            condition: 'workflow.can(this, "prepare")',
            workflow: 'order',
        ),
        new Link(
            operation: 'cancel',
            text: 'order.cancel',
            icon: 'bi:x',
            attributes: ['class' => 'btn btn-danger'],
            condition: 'workflow.can(this, "cancel")',
            workflow: 'order',
        ),
    ],
)
```

`WorkflowConditionMatcher` sees the link's `workflow` and injects a `workflow` variable —
the `Workflow` instance for the current row — into the expression context, plus
`workflow_transition` when the link declares one. Then the expression is evaluated with `this`
bound to the row.

Anything the Workflow API exposes is available:

```
workflow.can(this, "prepare")
workflow.getMarking(this).has("prepared")
```

Combine it with a role check:

```php
condition: 'is_granted("ROLE_ADMIN") and workflow.can(this, "cancel")'
```

## Displaying the state

A `Map` property turns the marking into a readable, translated label:

```php
new Map(
    name: 'state',
    dataTransformer: EnumDataTransformer::class,
    map: [
        'new' => 'order.state.new',
        'prepared' => 'order.state.prepared',
        'delivered' => 'order.state.delivered',
        'paid' => 'order.state.paid',
        'cancelled' => 'order.state.cancelled',
    ],
)
```

Drop `EnumDataTransformer` if the marking is a plain string rather than a backed enum.

## `WorkflowAwareInterface`

Metadata objects that participate carry
`LAG\AdminBundle\Workflow\WorkflowAwareInterface` — `getWorkflow()` and
`getWorkflowTransition()`. `Link` implements it; implement it on your own metadata classes to
get the same expression context.

## Next

[Uploads and images](uploads.md).
