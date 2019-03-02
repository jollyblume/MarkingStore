## jollyblume/workflow
##### This component provides a light weight, multi-tenant Marking Store for use with symfony/workflow.

* Implements **symfony/workflow** *MarkingStoreInterface* as a shim between a *Workflow* and it's marking store.
* A *UUID* is stored on a subject, instead of the subject's marking.
  * The property used to access a subject's uuid must not be *marking*. This property name is reserved by **symfony/workflow**. Its use will throw an exception.
* The shim communicates with a marking store through a mediator.
* The shim transforms between a *Marking* and an *array-of-places* (used by the mediator and marking store) as needed.
* The mediator is responsible for accessing the actual marking store.
* The current mediator dispatches events to get and set an *array-of-places* on a marking store.
  * This implementation has a known issue, where the shim can get or set a marking, not knowning there are no marking stores listening. Any marking's set, would not be persisted.

##### Mediator event's include three identifiers from the shim.
  * The shim's name will become $storeName.
  * The subject's *UUID* will become $subjectUuid.
  * The property used to store the *UUID* on a subject will become $property.

The InMemoryMarkings marking store persists markings in an array, where a marking's identifiers form the key and the *array-of-places" the value.

    $storeName>/$subjectUuid>/$property = [place1, place2, ...]

The mediator dispatches a *MarkingStoreEvent* to active listeners of four events.
* *workflow.store.created* is dispatched during construction of the shim.
* *workflow.places.get* is dispatched to get an *array-of-places*.
* *workflow.places.setting* is dispatched to set an *array-of-places*.
* *workflow.places.set* is dispatched after the *array-of-places* has be set. Listeners can perform cleanup code during this event (for instance, flushing a Doctrine entity).

#### Install via composer
composer.phar require jollyblume/workflow:@dev
