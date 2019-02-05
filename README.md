## jollyblume/workflow
###### This component adds a light weight, multi-tenant Marking Store to symfony/workflow.
This component is built on **symfony/workflow** and is a different implementation of symfony's *MarkingStoreInterface*

A simple key-value pair view of a marking can represented by storage strategies. The built-in InMemoryStorageStrategy is an example:

    <marking-store-id>/<subject-id> = <array-of-places>

**StorageStrategyInterface** is the mediator implementation required for a marking store to interact with a storage layer. Multiple strategies may be installed. If no strategies are installed, the marking store never persists anything and will return *[]* for any get.

**InMemoryStorageStrategy** is included and can support diverse needs, but does not actually persist anything. Extending it to support a strategy that persists to a user's session would be useful, but any more complicated should be defined in a separate library.

**MarkingStoreShim** implements the **symfony/workflow** **MarkingStoreCollection** interface and is sometime refered to as the marking store in this README.

The *shim* is a mediator between a **symfony/workflow** workflow object and a workflow's marking store. It dispatches **WorkflowEvent** to active listeners of four events.
* *workflow.store.created* is dispatched during construction of the marking store.
* There is a one-to-one relationship between *workflow* and *marking-store* objects.
* *workflow.places.get* is dispatched to get an array of places from the strategy for a *markingStorId* and *subjectId* pair.
* *workflow.places.setting* is dispatched to set an array of places to the strategy for a *markingStorId* and *subjectId* pair.
* *workflow.places.set* is dispatched after storage is completed for any cleanup code to execute after a storage step completes.

The *array-of-places* concept for a marking is used consistently on the marking store side of this component. However, the *shim* converts between a **symfony/workflow** **Marking** and these arrays. Symfony-side code never sees an *array-of-places*.

###### *Still a lot of work to do*
Current unit tests provide a reasonable code coverage and I don't expect any bugs. ;)
I would love a bug report or pr when you find one. Bug reports will get quick attention.

A few things need completing before a *final* release.
* github bug tracking and other tools
* this README
* a few unit tests.
* many code docblocks.

The component source is in its expected, final state. Other than the issues notes above, there are no changes scheduled for this library.

#### Install via composer
composer.phar require jollyblume/workflow:@dev
