# graph

A mathematical graph/network library written in PHP

## Examples

Initialize sample graph
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
$rome->createEdgeTo($rome); // create loop
````

Let's see which city (Vertex) has road (i.e. an edge pointing) to Rome
````php
foreach($rome->getVerticesEdgeFrom() as $vertex){
    echo $vertex->getId().' leads to rome'.PHP_EOL; // result: Madrid and Rome itself
}
````

Looking for more example scripts? Check out [flos/graph-php](https://github.com/flos/graph-php).

## Install

The recommended way to install this library is [through composer](http://getcomposer.org). [New to composer?](http://getcomposer.org/doc/00-intro.md)

```JSON
{
    "require": {
        "clue/graph": "dev-master"
    }
}
```

## Features

This library is built around the concept of [mathematical graph theory](http://en.wikipedia.org/wiki/Graph_%28mathematics%29) (i.e. it is **not** a [charting](http://en.wikipedia.org/wiki/Chart) library for drawing a [graph of a function](http://en.wikipedia.org/wiki/Graph_of_a_function)). In essence, a graph is a set of *nodes* with any number of *connections* inbetween. In graph theory, [vertices](http://en.wikipedia.org/wiki/Vertex_%28graph_theory%29) (plural of vertex) are an abstract representation of these *nodes*, while *connections* are represented as *edges*. Edges may be either undirected ("two-way") or directed ("one-way", aka di-edges, arcs).

Depending no how the edges are constructed, the whole graph can either be undirected, can be a [directed graph](http://en.wikipedia.org/wiki/Directed_graph) (aka digraph) or be a [mixed graph](http://en.wikipedia.org/wiki/Simple_graph#Mixed_graph). Edges are also allowed to form [loops](http://en.wikipedia.org/wiki/Loop_%28graph_theory%29) (i.e. an edge from vertex A pointing to vertex A again). Also, [multiple edges](http://en.wikipedia.org/wiki/Multiple_edges) from vertex A to vertex B  are supported as well (aka parallel edges), effectively forming a [multigraph](http://en.wikipedia.org/wiki/Multigraph) (aka pseudograph). And of course, any combination thereof is supported as well. While many authors try to differentiate between these core concepts, this library tries hard to not impose any artificial limitations or assumptions on your graphs.

The library supports visualizing graph images, including them into webpages, opening up images from within CLI applications and exporting them as PNG, JPEG or SVG file formats (among many others). Because [graph drawing](http://en.wikipedia.org/wiki/Graph_drawing) is a complex area on its own, the actual layouting of the graph is left up to the excelent [GraphViz](http://www.graphviz.org/) "Graph Visualization Software" and we merely provide some convenient APIs to interface with GraphViz.

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


## Tests

To run the test suite, you need PHPUnit. Go to the project root and run:
````
$ phpunit
````