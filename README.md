Multi-tenant Workflow Marking Store Component
=============================================

## General library status
The code base is stable and it's interfaces are not going to be changing. Unit tests are far from complete and many tests remain to be written. Existing test provide a credible level of code quality for development use. I don't expect any bugs
in the current code base. Of course, everyone knows how foolish that notion is until test units are complete...

This README needs to be completed, as do code docblocks.

I do not intend to release a 1.0 version until these todo's are complete.

## Install via composer
   composer.phar require jollyblume/workflow:@dev

## Library overview
This library implements a multi-tenant marking store meeting the requirements for a symfony/workflow marking store.

It is dependant on a few 3rd party vendors:
* symfony/workflow
* symfony/property-access
* symfony/event-dispatcher
* jollyblume/common

jollyblume/common is only included to support the InMemoryPeristStrategy and can be considered an implementation detail. This strategy is only included to provide compatibility with swf's InMemoryMarkingStore, symfony's default marking store implementation. Persistence strategies should be developed in isolated libraries. The InMemoryPersistStrategy dilutes the focus of this component, but provides a good reference implementation.

## Petri Nets and Symfony Workflow and This Component
### A super quick overview
Petri Nets have been used to descibe workflow systems since the 1960's. They have been studied deeply since and proven to provide superior workflow analytics and monitoring, along with advanced workflow design patterns.

The Petri Net model defines a few standard interfaces used to descibe a workflow and track a token as it is processed by that workflow:
* **Tokens** are the objects being tracked as it proceeds through a workflow. The symfony/workflow component uses the term *subject* in place of the traditional *token*. A symfony *subject* has minimal requirements to be considered valid workflow tokens. These requirements are discussed below. I am likely to use the terms *token* and *subject* interchangeably.  I will clean this language up during a future review cycle.
* **Places** are stopping points for a token as it proceeds through a workflow. A token can only be processed by a given *transition* when it is resting some specific *place*. A *token* must be located in at least one *place*, but may be in multiple *places*. A list of places for a token is called the token's marking.
* **Arcs** are connections between a one or more *places* and one or more *transitions* (and vice-versa). The symfony/workflow component greatly simplifies the standard Perti Net model by mergin *arc* concepts into its *transitions*.

Symfony documentation refers to its workflow model as a Petri Net subset and this is technically true. However, the concept or *arcs* is still a part of the symfony workflow architecture. It is just not a particularly visible concept. Symfony/workflow is a modern and effective workflow implementation. I look forward to playing with it.

Petri Net implementations often have similar system-level components and classes that make-up the overall workflow architecture:
* A *Workflow* is an active workflow, through which *tokens* are progressing. It is a live object or service used from user-land code to process a *token*. A single workflow object is used to process an unbounded number of tokens.
* A *Definition* is a collection of places and transitions. Definitions are used to create a given workflow object.
* A *Registry* is a collection of definitions and often a workflow object builder.
* A *Marking Store* is a database containing the token markings for a workflow. This marking data is extremely important to any production workflow system. It is the focus for workflow analytic and monitoring systems and is the system that makes workflow systems based on Petri Nets so powerful.

This handful of model and systems classes make up the core of any Petri Net based workflow system. The symfony/workflow component is no different. Its modern take on the core system and clean interface make this component an ideal workflow framework.

They also define general workflow domain specific terminology often used when descibing workflow systems.

There are literally thousands of hours of documentation and research papers to be found on the internet. It is a fascinating subject.

Of particular interest, once workflow fundamentals are understood, are several sites describing standard workflow design patterns. These workflow design patterns will provide a deeper understanding of the capabilities of workflow systems based on the Peri Net.

### A word about workflows and state machines
I've seen a lot of workflow systems try to implement a state machine as if it were a counterpart of the marking store. This concept will break the design of any workflow system, because the idea of state in a workflow is a myth.

By definition and design, the state of a single transition rarely impacts the state of another transition.

State machine mechanics don't belong anywhere in the workflow core. They are more closely related to a subject, where global state takes on relevance in the context of a subject or group of subjects places and inter-place relationships within the workflow.

I intend to implement a state machine architecture for subjects down the road. There will be no mention of state machines found in this marking store.

### Why this component
The symfony/workflow component is a framework for building workflow systems. It includes an elegant marking store interface, but only a minimal marking store implementation.

Its implementation stores a marking on a public property of the subject's class. It a solid and super-simple implementation. However, because the marking store is stored directly on the subject, allowing a subject to participate in multiple workflow simultaneously can be complicated.

This component is focused on tracking an unbounded number of workflows a subject can participate simultaneously. In addition, it describes a simple marking store persistence architecture. This marking store enables important workflow definition and management features, such as recursive workflows.

### Component goals
General goals and requirements defined for this project:
* The primary focus of this component is a drop-in replacement for the symfony/workflow marking store implementation supporting multi-tenant (multiple workflows per subject) marking stores.
* This component is little more than a mediator between swf and this marking store's namespaces.
* An event dispatcher allows the marking store class to communicate with persistence code. A simple persistStrategyInterface is implemented by the persistence layer. How these persitence strategies are implemented will vary wildly.
* A simple in memory persistence strategy will be included with this component. However, further strategies will be delivered in seperate libraries.
* Implement property access for working with properties on subjects, similar to the swf component.
* Don't walk on the swf created marking on a subject. Play nice with other system's that may be using the symfony marking store implementation. This component uses a similar system to mark a subject, but its default property name is *subjectId*.

### Component architecture
The marking store class implements swf's marking store interface. Instead of storing a marking in the *marking* property on the subject, a single UUID is stored in the *subjectId* property on the subject. Once set, it will uniquely identify the subject to the persistence layer and never changes. The subjectId is invisible to the swf code and is basically an implementation detail.

A second UUID is assigned to each marking store when it is created and store locally in a private property of the marking store class.

The combination of the marking store id and the subject id uniquely identify every marking in the store.

When the swf workflow object executes a transition it will move the subject to a different place. The workflow uses the marking store's getMark and setMark methods for access to the marking for a subject. These accessor methods simply dispatch a number of events during get and set operations to interact with the actual persistence layer:
* workflow.store.created notifies any persistence listeners that a new marking store was created. Since there is a one-to-one relationship between a workflow and its marking store, this id is also a side-channel id to a single workflow.
* workflow.places.get requests the marking for a store and subject.
* workflow.places.setting notifies the persistence layer that a marking needs to be persisted.
* workflow.places.set notifies the persistence layer that a marking was set and any required persistence cleanup (such as flushing) can be performed.
