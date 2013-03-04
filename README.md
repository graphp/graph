# graph

A mathematical graph/network library written in PHP

[![Build Status](https://travis-ci.org/clue/graph.png)](https://travis-ci.org/clue/graph)

While the APIs should be stable, this library is still considered *beta* software. Please see *Contributing* below for ways you can help out.

## Quickstart examples

Once [installed](#install), let's initialize a sample graph:

````php
<?php
require_once 'vendor/autoload.php';

use \Fhaculty\Graph\Graph as Graph;

$graph = new Graph();

// create some cities
$rome = $graph->createVertex('Rome');
$madrid = $graph->createVertex('Madrid');
$cologne = $graph->createVertex('Cologne');

// build some roads
$cologne->createEdgeTo($madrid);
$madrid->createEdgeTo($rome);
// create loop
$rome->createEdgeTo($rome);
````

Let's see which city (Vertex) has road (i.e. an edge pointing) to Rome
````php
foreach ($rome->getVerticesEdgeFrom() as $vertex) {
    echo $vertex->getId().' leads to rome'.PHP_EOL;
    // result: Madrid and Rome itself
}
````

Looking for more example scripts? Check out [flos/graph-php](https://github.com/flos/graph-php).

## Features

This library is built around the concept of [mathematical graph theory](http://en.wikipedia.org/wiki/Graph_%28mathematics%29) (i.e. it is **not** a [charting](http://en.wikipedia.org/wiki/Chart) library for drawing a [graph of a function](http://en.wikipedia.org/wiki/Graph_of_a_function)). In essence, a graph is a set of *nodes* with any number of *connections* inbetween. In graph theory, [vertices](http://en.wikipedia.org/wiki/Vertex_%28graph_theory%29) (plural of vertex) are an abstract representation of these *nodes*, while *connections* are represented as *edges*. Edges may be either undirected ("two-way") or directed ("one-way", aka di-edges, arcs).

Depending no how the edges are constructed, the whole graph can either be undirected, can be a [directed graph](http://en.wikipedia.org/wiki/Directed_graph) (aka digraph) or be a [mixed graph](http://en.wikipedia.org/wiki/Simple_graph#Mixed_graph). Edges are also allowed to form [loops](http://en.wikipedia.org/wiki/Loop_%28graph_theory%29) (i.e. an edge from vertex A pointing to vertex A again). Also, [multiple edges](http://en.wikipedia.org/wiki/Multiple_edges) from vertex A to vertex B  are supported as well (aka parallel edges), effectively forming a [multigraph](http://en.wikipedia.org/wiki/Multigraph) (aka pseudograph). And of course, any combination thereof is supported as well. While many authors try to differentiate between these core concepts, this library tries hard to not impose any artificial limitations or assumptions on your graphs.

### Graph drawing

The library supports visualizing graph images, including them into webpages, opening up images from within CLI applications and exporting them as PNG, JPEG or SVG file formats (among many others). Because [graph drawing](http://en.wikipedia.org/wiki/Graph_drawing) is a complex area on its own, the actual layouting of the graph is left up to the excelent [GraphViz](http://www.graphviz.org/) "Graph Visualization Software" and we merely provide some convenient APIs to interface with GraphViz.

### Common algorithms

Besides graph drawing, one of the most common things to do with graphs is running algorithms to solve common graph problems. Therefor this library includes implementations for a number of commonly used graph algorithms:

* Search
    * Deep first (DFS)
    * Breadth first search (BFS)
* Shortest path
    * Dijkstra
    * Moore-Bellman-Ford (MBF)
    * Counting number of hops (simple BFS)
* Minimum spanning tree (MST)
    * Kruskal
    * Prim
* Traveling salesman problem (TSP)
    * Bruteforce algorithm
    * Minimum spanning tree heuristic (TSP MST heuristic)
    * Nearest neighbor heuristic (NN heuristic)
* Maximum flow
    * Edmonds-Karp
* Minimum cost flow (MCF)
    * Cycle canceling
    * Successive shortest path
* Maximum matching
    * Flow algorithm

## Install

The recommended way to install this library is [through composer](http://getcomposer.org). [New to composer?](http://getcomposer.org/doc/00-intro.md)

```JSON
{
    "require": {
        "clue/graph": "dev-master"
    }
}
```

Optional recommendation:
In order to be able to use the [graph drawing feature](#graph-drawing) you'll have to
install GraphViz (`dot` executable). Users of Debian/Ubuntu-based distributions may simply
invoke `sudo apt-get install graphviz`, Windows users have to
[download GraphViZ for Windows](http://www.graphviz.org/Download_windows.php) and remaining
users should install from [GraphViz homepage](http://www.graphviz.org/Download.php).

## Tests

To run the test suite, you need PHPUnit. Go to the project root and run:
````
$ phpunit
````

## Contributing

As stated above, this library is still considered beta software and requires some contributions. While constructing graphs, plotting them and running all algorithms on them has been tested with sample graphs, there's a fair chance that we're missing some special cases for *your* graphs - partly because of some implied assumptions and mostly because of a lack of basic unit tests.

If you encounter any issues, please don't hesitate to drop us a line, file a bug report or even best provide us with a patch / pull request and/or unit test to reproduce your problem.

Besides directly working with the code, any additional documentation, additions to our readme or even fixing simple typos are appreciated just as well.

Any feedback and/or contribution is welcome!
