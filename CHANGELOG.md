# CHANGELOG

This file is a manually maintained list of changes for each release. Feel free
to add your changes here when sending pull requests. Also send corrections if
you spot any mistakes.

## 0.6.0 (2013-xx-xx)

* BC break: `Graph::createVertices()` now returns an array of vertices instead of the chainable `Graph` (#19)
* BC break: Move `Set::getWeight()`, `Set::getWeightFlow()`, `Set::getWeightMin()` and `Set::isWeighted()` to new `Algorithm\Weight` (#33)
* BC break: Move `Graph::getDegree()`, `Graph::getDegreeMin()`, `Graph::getDegreeMax()`, `Graph::isRegular()` and `Graph::isBalanced()` to new `Algorithm\Degree` (#29)
* BC break: Move `Graph::getBalance()` and `Graph::isBalancedFlow()` to new `Algorithm\Balance` (#30)
* Feature: `Graph::createVertices()` now also accepts an array of vertex IDs (#19)
* Fix: Various issues with `Vertex`/`Edge` layout attributes (#32)

## 0.5.0 (2013-05-07)

* First tagged release (See issue #20 for more info on why it starts as v0.5.0)
