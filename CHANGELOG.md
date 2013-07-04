# CHANGELOG

This file is a manually maintained list of changes for each release. Feel free
to add your changes here when sending pull requests. Also send corrections if
you spot any mistakes.

## 0.6.0 (2013-xx-xx)

* BC break: Move algorithm definitions in base classes to separate algorithm classes ([#27](https://github.com/clue/graph/issues/27)).
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

* BC break: Remove unneeded algorithm alias definitions ([#31](https://github.com/clue/graph/issues/31)). The following *alias definitions*
have been removed, their original/actual name has already existed before and continues to work
unchanged. Update your references if applicable:

| Old/removed alias definition | Actual name |
|---|---|
| `Graph::isConnected()` | `Algorithm\ConnectedComponents::isSingle()` |
| `Graph::hasEulerianCycle()` | `Algorithm\Eulerian::hasCycle()` |
| `Graph::getNumberOfComponents()` | `Algorithm\ConnectedComponents::getNumberOfComponents()` |
| `Graph::getNumberOfGroups()` | `Algorithm\Groups::getNumberOfGroups()` |
| `Graph::isBipartit()` | `AlgorithmBipartit::isBipartit()` |

* BC break: `Graph::createVertices()` now returns an array of vertices instead of the chainable `Graph` ([#19](https://github.com/clue/graph/issues/19))
* BC break: Move `Loader\UmlClassDiagram` to separate [fhaculty/graph-uml](https://github.com/fhaculty/graph-uml) repo ([#38](https://github.com/clue/graph/issues/38))
* BC break: Remove needless `Algorithm\MinimumSpanningTree\PrimWithIf` (use `Algorithm\MinimumSpanningTree\Prim` instead) ([#45](https://github.com/clue/graph/issues/45))
* BC break: `Vertex::createEdgeTo()` now returns an instance of type `Edge\Undirected` instead of `Edge\UndirectedId` ([#46](https://github.com/clue/graph/issues/46))
* Feature: New `Algorithm\Tree` namespace with algorithms for undirected and directed, rooted trees ([#44](https://github.com/clue/graph/issues/44))
* Feature: According to be above list of moved algorithm methods, the following algorithm classes have been added ([#27](https://github.com/clue/graph/issues/27)):
    * New `Algorithm\Weight` ([#33](https://github.com/clue/graph/issues/33))
    * New `Algorithm\Degree` ([#29](https://github.com/clue/graph/issues/29), [#49](https://github.com/clue/graph/issues/49))
    * New `Algorithm\Directed` ([#34](https://github.com/clue/graph/issues/34))
    * New `Algorithm\Symmetric` ([#41](https://github.com/clue/graph/issues/41))
    * New `Algorithm\Complete` ([#43](https://github.com/clue/graph/issues/43))
    * New `Algorithm\Flow` ([#30](https://github.com/clue/graph/issues/30), [#47](https://github.com/clue/graph/issues/47))
    * New `Algorithm\Tree` ([#44](https://github.com/clue/graph/issues/44))
    * New `Algorithm\Loop` ([#51](https://github.com/clue/graph/issues/51))
* Feature: `Graph::createVertices()` now also accepts an array of vertex IDs ([#19](https://github.com/clue/graph/issues/19))
* Fix: Various issues with `Vertex`/`Edge` layout attributes ([#32](https://github.com/clue/graph/issues/32))

## 0.5.0 (2013-05-07)

* First tagged release (See issue [#20](https://github.com/clue/graph/issues/20) for more info on why it starts as v0.5.0)
