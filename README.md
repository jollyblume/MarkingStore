Multi-tenant Workflow Marking Store Component
=============================================

## General library status
The code base is stable and it's interfaces are not going to be changing. Unit tests are far from complete, many tests remain to be written. Existing tests provide a credible level of code quality for development use. I don't expect any bugs
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
* **Places** are stopping points for a token as it proceeds through a workflow. A token can only be processed by a given *transition* when it is resting some specific *place(s)*. A *token* must be located in at least one *place*, but may be in multiple *places*. The list of place(s), where a token is located, is known as the token's marking. How markings relate to each other is often the core of analytic and monitoring systems.
* **Arcs** are connections between a *place(s)* and a *transition(s)*. The symfony/workflow component greatly simplifies the standard Perti Net model by merging Petri Net *arc* concepts into its *transition* architecture.

Petri Net implementations often have similar system-level components and classes that make-up the overall workflow architecture:
* A **Workflow** is an *active* workflow, through which **tokens** are progressing. It is a live object or service used from user-land code to process a **token**. A single **workflow** object is used to process an unbounded number of **tokens** concurrently.
* A **Definition** is a collection of **places** and **transitions**. **Definitions** are used to create a new **workflow**.
* A **Registry** is a collection of **definitions** and often a workflow object builder.
* A **Marking Store** is a database containing **token(s)** **marking(s)** for a **workflow**. This marking data is extremely important to any production workflow system. It is the focus for workflow analytic and monitoring systems and is is one of a Petri Net's most powerful feature.

This handful of model and systems classes make up the core of many Petri Net based automation and workflow systems. The symfony/workflow component is no different. Its modern take on the core system and clean interface make this component an ideal workflow framework.

They also define general workflow domain specific terminology often used when descibing workflow systems.

There are literally thousands of hours of documentation and research papers to be found on the internet related to these subjects.

Of particular interest are several sites describing standard workflow design patterns. These workflow design patterns will provide a deeper understanding of the capabilities of workflow systems based on the Peri Net concepts.

### A word about workflows and state machines
I've seen a lot of workflow systems try to implement a state machine as if it were a counterpart of the marking store. This concept will break the design of any workflow system, because the idea of state in a workflow is misunderstood.

By definition and design, the state of a single transition rarely impacts the state of another transition. The state most commonly shared occurs during a **transition**, where state at one **place** causes some state in the next **place**. This feature is also largely responsible for a workflow to be (at least mathematically) reversible.

State machine mechanics don't belong anywhere in the workflow core. They are more closely related to a subject, where global state takes on relevance in the context of a subject or group of subjects places and inter-place relationships within a workflow.

I intend to implement a state machine architecture for subjects down the road. State Machines are an important workflow tool. But the conversations belong in some application view of the workflow. It is not a subject for this component, however.

My initial conept for a state machine involves creating state controlling **workflow(s)**, where the **subject(s)** for this **workflow(s)** is an internal implementation detail. I expect this, or a similar solution, will allow a clean state machine implementation that is largely independant of any workflow or subject interface. It will also have the benefit of any analytics and management tools built for a workflow system as a whole.

### Why this component
The symfony/workflow component is a framework for building workflow systems. It includes an elegant marking store interface, but only a minimal marking store implementation.

Its implementation stores a marking on a public property of a subject. It a solid and super-simple implementation. However, because the marking store is stored directly on a subject, it can not participate in multiple workflows concurrently. The decentralization of the **marking(s)** also hides important side-channel workflow metadata.

This component is focused on tracking the **marking(s)** for an unbounded number of **workflows)** a **subject** is participating in, concurrently. In addition, it describes a simple marking store persistence architecture. This marking store enables important workflow definition and management features, such as recursive workflows.

### Component goals and architecture
General goals and requirements for this project include:
* This component implements symfony/workflow::MarkingStoreInterface, the interface used **workflow(s)** to access **marking(s)**. The jbj/workflow::MarkingStoreShim implements a mediator pattern that manages this component edge.
* This component implement a marking store capable of persisting **marking(s)** for **subject(s)**. This multi-tenant architecture is important for implementing important workflow definition strategies, such as *Recursive Workflow(s)** and status bubbling.
* This component employs an event dispatcher for all accessor requirements between the jbj/workflow::MarkingStoreShim and any persistence layers that may have been defined. In fact, it completely oblivious to what persistence workflow may be occurring.
* Persistence strategies must implement jbj/workflow::PersistStrategyInterface. This interface closely resembles symfony/workflow::MarkingStoreInterface, but allows for the additional metadata required for multi-tenancy.
* Persistence strategies should be delivered indiviually within isolated libraries. Vendors used by one strategy will vary wildy from those of another. A persistence strategy only needs to implement jbj/workflow::PersistStrategyInterface as its entry point.
* This component implements the same pattern for marking a subject as the symfony/workflow component. Where symfony/workflow marks subject with a property named *marking*, this component uses a property named *subjectId*. Ths should allow any code base using symfony's default marking store implementation to coexist with this component.
* All ids (marking store id and subject id) should be UUID's.

When **workflow** executes a transition it will move a subject to a different **place(s)**. The jbj/workflow::MarkingStoreShim uses the symfony/workflow::MarkingStoreInterface methods getMark($subject) and setMark($subject)  as expected and uses these methods to dispatch appropriate events:
  * *workflow.store.created* notifies any listeners that a new marking store was created.The one-to-one relationship between a **workflow** and a **marking store** is important side-channel metadata.
  * *workflow.places.get* requests the **marking** for a store and subject combination. Persistence layes appear to be simple key stores that combine the store and subject ids to create an aggregate key whose value is an array of places. The concept of an array of places doesn't become a **marking** until it crosses the Shim mediator.
  * *workflow.places.setting* notifies the persistence layer that a marking needs to be persisted.
  * workflow.places.set notifies the persistence layer that a persistence is complete and cleanup steps (such as flushing) can be performed.
