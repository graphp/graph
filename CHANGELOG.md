# CHANGELOG

This file is a manually maintained list of changes for each release. Feel free
to add your changes here when sending pull requests. Also send corrections if
you spot any mistakes.

## 0.7.0 (2013-xx-xx)

*   Feature: Add new `Set\Vertices` and `Set\Edges` classes that handle common
    operations on a Set of multiple `Vertex` and `Edge` instances respectively.
    ([#48](https://github.com/clue/graph/issues/48))

*   BC break: Move operations and their corresponding constants concerning Sets
    to their corresponding Sets:

    | Old name | New name |
    |---|---|
    | `Edge\Base::getFirst()` | `Set\Edges::getEdgeOrder()` |
    | `Edge\Base::getAll()` | `Set\Edges::getEdgesOrder()` |
    | `Edge\Base::ORDER_*` | `Set\Edges::ORDER_* |
    |---|---|
    | `Vertex::getFirst()` | `Set\Vertices::getVertexOrder()` |
    | `Vertex::getAll()` | `Set\Vertices::getVerticesOrder()` |
    | `Vertex::ORDER_` | `Set\Vertices::ORDER_*` |

*   BC break: Each `getVertices*()` and `getEdges*()` method now returns a `Set`
    instead of a primitive array of instances. *Most* of the time this should
    work without changing your code, because each `Set` implements an `Iterator`
    interface and can easily be iterated using `foreach`. However, using a `Set`
    instead of a plain array differs when checking its boolean value or
    comparing two Sets. I.e. if you happen to want to check if an `Set` is empty,
    you now have to use the more explicit syntax `$set->isEmpty()`.
    
*   BC break: `Vertex::getVertices()`, `Vertex::getVerticesEdgeTo()` and
    `Vertex::getVerticesEdgeFrom()` now return a `Set\Vertices` instance that
    may contain duplicate vertices if parallel (multiple) edges exist. Previously
    there was no easy way to detect this situation - this is now the default. If
    you also want to get unique / distinct `Vertex` instances, use
    `Vertex::getVertices()->getVerticesDistinct()` where applicable.

*   BC break: Remove all occurances of `getVerticesId()`, use
    `getVertices()->getIds()` instead.

*   BC break: Merge `Cycle` into `Walk` ([#61](https://github.com/clue/graph/issues/61)).
    As such, its static factory methods had to be renamed. Update your references if applicable:

    | Old name | New name |
    |---|---|
    | `Cycle::factoryFromPredecessorMap()` | `Walk::factoryCycleFromPredecessorMap()` |
    | `Cycle::factoryFromVertices()` | `Walk::factoryCycleFromVertices()` |
    | `Cycle::factoryFromEdges()` | `Walk::factoryCycleFromEdges()` |

*   BC break: Remove `Graph::isEmpty()` because it's not well-defined and might
    be confusing. Most literature suggests it should check for existing edges,
    whereas the old behavior was to check for existing vertices instead. Use either
    of the more transparent methods
    `Algorithm\Property\GraphProperty::isNull()` (old behavior) or (where applicable)
    `Algorithm\Property\GraphProperty::isEmpty()` ([#59](https://github.com/clue/graph/issues/59)).

*   BC break: Each of the above methods (`Walk::factoryCycleFromPredecessorMap()`,
    `Walk::factoryCycleFromVertices()`, `Walk::factoryCycleFromEdges()`) now
    actually makes sure the returned `Walk` instance is actually a valid Cycle,
    i.e. the start `Vertex` is the same as the end `Vertex` ([#61](https://github.com/clue/graph/issues/61))

*   BC break: Each `Algorithm\ShortestPath` algorithm now consistenly does not
    return a zero weight for the root Vertex and now supports loop edges on the root
    Vertex ([#62](https://github.com/clue/graph/issues/62))

*   BC break: Each `Algorithm\ShortestPath` algorithm now consistently throws an
    `OutOfBoundsException` for unreachable vertices
    ([#62](https://github.com/clue/graph/issues/62))

*   BC break: A null Graph (a Graph with no Vertices and thus no Edges) is not a
    valid tree (because it is not connected), adjust `Algorithm\Tree\Base::isTree()`
    accordingly.
    ([#72](https://github.com/clue/graph/issues/72))

*   Feature: Add `Algorithm\ShortestPath::hasVertex(Vertex $vertex)` to check whether
    a path to the given Vertex exists ([#62](https://github.com/clue/graph/issues/62)).

*   Feature: Support opening GraphViz images on Mac OS X in default image viewer
    ([#67](https://github.com/clue/graph/issues/67) @onigoetz)

*   Feature: Add `Walk::factoryFromVertices()`
    ([#64](https://github.com/clue/graph/issues/64)).

*   Fix: Checking `Walk::isValid()`
    ([#61](https://github.com/clue/graph/issues/61))

*   Fix: Missing import prevented
    `Algorithm\ShortestPath\MooreBellmanFord::getCycleNegative()` from actually
    throwing the right `UnderflowException` if no cycle was actually found
    ([#62](https://github.com/clue/graph/issues/62))

*   Fix: Calling `Exporter\Image::setFormat()` had no effect due to misassignment
    ([#70](https://github.com/clue/graph/issues/70) @FGM)

## 0.6.0 (2013-07-11)

*   BC break: Move algorithm definitions in base classes to separate algorithm classes ([#27](https://github.com/clue/graph/issues/27)).
    The following methods containing algorithms were now moved to separate algorithm classes. This
    change encourages code-reuse, simplifies spotting algorithms, helps reducing complexity,
    improves testablity and avoids tight coupling. Update your references if applicable:

    | Old name | New name | Related ticket |
    |---|---|---|
    | `Set::getWeight()` | `Algorithm\Weight::getWeight()` | [#33](https://github.com/clue/graph/issues/33) |
    | `Set::getWeightFlow()` | `Algorithm\Weight::getWeightFlow()` | [#33](https://github.com/clue/graph/issues/33) |
    | `Set::getWeightMin()` | `Algorithm\Weight::getWeightMin()` | [#33](https://github.com/clue/graph/issues/33) |
    | `Set::isWeighted()` | `Algorithm\Weight::isWeighted()` | [#33](https://github.com/clue/graph/issues/33) |
    |-|-|-|
    | `Graph::getDegree()` | `Algorithm\Degree::getDegree()` | [#29](https://github.com/clue/graph/issues/29) |
    | `Graph::getDegreeMin()` | `Algorithm\Degree::getDegreeMin()` | [#29](https://github.com/clue/graph/issues/29) |
    | `Graph::getDegreeMax()` | `Algorithm\Degree::getDegreeMax()` | [#29](https://github.com/clue/graph/issues/29) |
    | `Graph::isRegular()` | `Algorithm\Degree::isRegular()` | [#29](https://github.com/clue/graph/issues/29) |
    | `Graph::isBalanced()` | `Algorithm\Degree::isBalanced()` | [#29](https://github.com/clue/graph/issues/29) |
    | `Vertex::getDegree()` | `Algorithm\Degree:getDegreeVertex()` | [#49](https://github.com/clue/graph/issues/49) |
    | `Vertex::getDegreeIn()` | `Algorithm\Degree:getDegreeInVertex()` | [#49](https://github.com/clue/graph/issues/49) |
    | `Vertex::getDegreeOut()` | `Algorithm\Degree:getDegreeOutVertex()` | [#49](https://github.com/clue/graph/issues/49) |
    | `Vertex::isSink()` | `Algorithm\Degree:isVertexSink()` | [#49](https://github.com/clue/graph/issues/49) |
    | `Vertex::isSource()` | `Algorithm\Degree:isVertexSource()` | [#49](https://github.com/clue/graph/issues/49) |
    | `Vertex::isIsolated()` | `Algorithm\Degree::isVertexIsolated()` | [#49](https://github.com/clue/graph/issues/49) |
    |-|-|-|
    | `Set::isDirected()` | `Algorithm\Directed::isDirected()` | [#34](https://github.com/clue/graph/issues/34) |
    |-|-|-|
    | `Graph::isSymmetric()` | `Algorithm\Symmetric::isSymmetric()` | [#41](https://github.com/clue/graph/issues/41) |
    |-|-|-|
    | `Graph::isComplete()` | `Algorithm\Complete::isComplete()` | [#43](https://github.com/clue/graph/issues/43) |
    |-|-|-|
    | `Set::hasFlow()` | `Algorithm\Flow::hasFlow()` | [#47](https://github.com/clue/graph/issues/47) |
    | `Graph::getBalance()` | `Algorithm\Flow::getBalance()` | [#30](https://github.com/clue/graph/issues/30), [#47](https://github.com/clue/graph/issues/47) |
    | `Graph::isBalancedFlow()` | `Algorithm\Flow::isBalancedFlow()` | [#30](https://github.com/clue/graph/issues/39), [#47](https://github.com/clue/graph/issues/47) |
    | `Vertex::getFlow()` | `Algorithm\Flow::getFlowVertex()` | [#47](https://github.com/clue/graph/issues/47) |
    |-|-|-|
    | `Vertex::isLeaf()` | `Algorithm\Tree\Undirected::isVertexLeaf()` | [#44](https://github.com/clue/graph/issues/44) |
    |-|-|-|
    | `Set::hasLoop()` | `Algorithm\Loop::hasLoop()` | [#51](https://github.com/clue/graph/issues/51) |
    | `Vertex::hasLoop()` | `Algorithm\Loop::hasLoopVertex()` | [#51](https://github.com/clue/graph/issues/51) |
    |-|-|-|
    | `Set::hasEdgeParallel()` | `Algorithm\Parallel::hasEdgeParallel()` | [#52](https://github.com/clue/graph/issues/52) |
    | `Edge\Base::hasEdgeParallel()` | `Algorithm\Parallel::hasEdgeParallelEdge()` | [#52](https://github.com/clue/graph/issues/52) |
    | `Edge\Base::getEdgesParallel()` | `Algorithm\Parallel::getEdgeParallelEdge()` | [#52](https://github.com/clue/graph/issues/52) |
    |-|-|-|
    | `Graph::isEdgeless()` | `Algorithm\Property\GraphProperty::isEdgeless()` | [#54](https://github.com/clue/graph/issues/54) |
    | `Graph::isTrivial()` | `Algorithm\Property\GraphProperty::isTrivial()` | [#54](https://github.com/clue/graph/issues/54) |
    | `Walk::isCycle()` | `Algorithm\Property\WalkProperty::isCycle()` | [#54](https://github.com/clue/graph/issues/54) |
    | `Walk::isPath()` | `Algorithm\Property\WalkProperty::isPath()` | [#54](https://github.com/clue/graph/issues/54) |
    | `Walk::hasCycle()` | `Algorithm\Property\WalkProperty::hasCycle()` | [#54](https://github.com/clue/graph/issues/54) |
    | `Walk::isLoop()` | `Algorithm\Property\WalkProperty::isLoop()` | [#54](https://github.com/clue/graph/issues/54) |
    | `Walk::isDigon()` | `Algorithm\Property\WalkProperty::isDigon()` | [#54](https://github.com/clue/graph/issues/54) |
    | `Walk::isTriangle()` | `Algorithm\Property\WalkProperty::isTriangle()` | [#54](https://github.com/clue/graph/issues/54) |
    | `Walk::isSimple()` | `Algorithm\Property\WalkProperty::isSimple()` | [#54](https://github.com/clue/graph/issues/54) |
    | `Walk::isHamiltonian()` | `Algorithm\Property\WalkProperty::isHamiltonian()` | [#54](https://github.com/clue/graph/issues/54) |
    | `Walk::isEulerian()` | `Algorithm\Property\WalkProperty::isEulerian()` | [#54](https://github.com/clue/graph/issues/54) |

*   BC break: Remove unneeded algorithm alias definitions ([#31](https://github.com/clue/graph/issues/31), [#50](https://github.com/clue/graph/issues/50)). The following *alias definitions*
    have been removed, their original/actual name has already existed before and continues to work
    unchanged. Update your references if applicable:

    | Old/removed alias definition | Actual name |
    |---|---|
    | `Graph::isConnected()` | `Algorithm\ConnectedComponents::isSingle()` |
    | `Graph::hasEulerianCycle()` | `Algorithm\Eulerian::hasCycle()` |
    | `Graph::getNumberOfComponents()` | `Algorithm\ConnectedComponents::getNumberOfComponents()` |
    | `Graph::getNumberOfGroups()` | `Algorithm\Groups::getNumberOfGroups()` |
    | `Graph::isBipartit()` | `Algorithm\Bipartit::isBipartit()` |
    | `Vertex::hasPathTo()` | `Algorithm\ShortestPath\BreadthFirst::hasVertex()` |
    | `Vertex::hasPathFrom()` | `Algorithm\ShortestPath\BreadthFirst::hasVertex()` |
    | `Vertex::getVerticesPathTo()` | `Algorithm\ShortestPath\BreadthFirst::getVertices()` |
    | `Vertex::getVerticesPathFrom()` | `Algorithm\ShortestPath\BreadthFirst::getVertices()` |

*   BC break: `Graph::createVertices()` now returns an array of vertices instead of the
    chainable `Graph` ([#19](https://github.com/clue/graph/issues/19))

*   BC break: Move `Loader\UmlClassDiagram` to separate [fhaculty/graph-uml](https://github.com/fhaculty/graph-uml)
    repo ([#38](https://github.com/clue/graph/issues/38))

*   BC break: Remove needless `Algorithm\MinimumSpanningTree\PrimWithIf`
    (use `Algorithm\MinimumSpanningTree\Prim` instead)
    ([#45](https://github.com/clue/graph/issues/45))

*   BC break: `Vertex::createEdgeTo()` now returns an instance of type
    `Edge\Undirected` instead of `Edge\UndirectedId`
    ([#46](https://github.com/clue/graph/issues/46))

*   BC break: `Edge\Base::setCapacity()` now consistently throws an `RangeException`
    instead of `InvalidArgumentException` if the current flow exceeds the new maximum
    capacity ([#53](https://github.com/clue/graph/issues/53))

*   Feature: New `Algorithm\Tree` namespace with algorithms for undirected and directed,
    rooted trees ([#44](https://github.com/clue/graph/issues/44))

*   Feature: According to be above list of moved algorithm methods, the following algorithm
    classes have been added ([#27](https://github.com/clue/graph/issues/27)):
    *   New `Algorithm\Weight` ([#33](https://github.com/clue/graph/issues/33))
    *   New `Algorithm\Degree` ([#29](https://github.com/clue/graph/issues/29), [#49](https://github.com/clue/graph/issues/49))
    *   New `Algorithm\Directed` ([#34](https://github.com/clue/graph/issues/34))
    *   New `Algorithm\Symmetric` ([#41](https://github.com/clue/graph/issues/41))
    *   New `Algorithm\Complete` ([#43](https://github.com/clue/graph/issues/43))
    *   New `Algorithm\Flow` ([#30](https://github.com/clue/graph/issues/30), [#47](https://github.com/clue/graph/issues/47))
    *   New `Algorithm\Tree` ([#44](https://github.com/clue/graph/issues/44))
    *   New `Algorithm\Loop` ([#51](https://github.com/clue/graph/issues/51))
    *   New `Algorithm\Parallel` ([#52](https://github.com/clue/graph/issues/52))
    *   New `Algorithm\Property` ([#54](https://github.com/clue/graph/issues/54))

*   Feature: `Graph::createVertices()` now also accepts an array of vertex IDs
    ([#19](https://github.com/clue/graph/issues/19))

*   Feature: Add `Algorithm\Property\WalkProperty::hasLoop()` alias definition for
    completeness ([#54](https://github.com/clue/graph/issues/54))

*   Feature: Add `Algorithm\Property\WalkProperty::isCircuit()` definition to distinguish
    circuits from cycles ([#54](https://github.com/clue/graph/issues/54))

*   Fix: Checking hamiltonian cycles always returned false
    ([#54](https://github.com/clue/graph/issues/54))

*   Fix: A Walk with no edges is no longer considered a valid cycle
    ([#54](https://github.com/clue/graph/issues/54))

*   Fix: Various issues with `Vertex`/`Edge` layout attributes
    ([#32](https://github.com/clue/graph/issues/32))

*   Fix: Getting multiple parallel edges for undirected edges
    ([#52](https://github.com/clue/graph/issues/52))

## 0.5.0 (2013-05-07)

*   First tagged release (See issue [#20](https://github.com/clue/graph/issues/20) for more info on why it starts as v0.5.0)
