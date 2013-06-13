# CHANGELOG

This file is a manually maintained list of changes for each release. Feel free
to add your changes here when sending pull requests. Also send corrections if
you spot any mistakes.

## 0.6.0 (2013-xx-xx)

* BC break: `Graph::createVertices()` now returns an array of vertices instead of the chainable `Graph` (#19)
* BC break: Move `Set::getWeight()`, `Set::getWeightFlow()`, `Set::getWeightMin()` and `Set::isWeighted()` to new `Algorithm\Weight` (#33)
* BC break: Move `Graph::getDegree()`, `Graph::getDegreeMin()`, `Graph::getDegreeMax()`, `Graph::isRegular()` and `Graph::isBalanced()` to new `Algorithm\Degree` (#29)
* BC break: Move `Graph::getBalance()` and `Graph::isBalancedFlow()` to new `Algorithm\Balance` (#30)
* BC break: Move `Set::isDirected()` to new `Algorithm\Directed` (#34)
* BC break: Move `Graph::isSymmetric()` to new `Algorithm\Symmetric` (#41)
* BC break: Move `Graph::isComplete()` to new `Algorithm\Complete` (#43)
* BC break: Remove unneeded algorithm alias definitions to reduce complexity, improve testability and avoid tight coupling (#31)
  * `Graph::isConnected()` (=> `Algorithm\ConnectedComponents::isSingle()`)
  * `Graph::hasEulerianCycle()` (=> `Algorithm\Eulerian::hasCycle()`)
  * `Graph::getNumberOfComponents()` (=> `Algorithm\ConnectedComponents::getNumberOfComponents()`)
  * `Graph::getNumberOfGroups()` (=> `Algorithm\Groups::getNumberOfGroups()`)
  * `Graph::isBipartit()` (=> `AlgorithmBipartit::isBipartit()`)
* BC break: Move `Loader\UmlClassDiagram` to separate [fhaculty/graph-uml](https://github.com/fhaculty/graph-uml) repo (#38)
* BC break: Remove needless `Algorithm\MinimumSpanningTree\PrimWithIf` (use `Algorithm\MinimumSpanningTree\Prim` instead) (#45)
* Feature: `Graph::createVertices()` now also accepts an array of vertex IDs (#19)
* Fix: Various issues with `Vertex`/`Edge` layout attributes (#32)

## 0.5.0 (2013-05-07)

* First tagged release (See issue #20 for more info on why it starts as v0.5.0)
