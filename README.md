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

*   Loading graphs (from plain text adjacency lists or edge lists)
*   Run algorithms
    * Search
        * Deep first (DFS)
        * Breadth first search (BFS)
    * Traveling salesman problem (TSP)
        * Bruteforce algorithm
        * Minimum spanning tree heuristic (TSP MST heuristic)
        * Nearest neighbor heuristic (NN heuristic)
    * Minimum spanning tree (MST)
        * Kruskal
        * Prim
    * Shortest path
        * Dijkstra
        * Moore-Bellman-Ford (MBF)
        * Counting number of hops (simple BFS)
    * Maximum flow
        * Edmonds-Karp
    * Minimum cost flow (MCF)
        * Cycle canceling
        * Successive shortest path
    * Maximum matching
        * Flow algorithm
*   Plotting with GraphViz (local installation needed)

## Tests

To run the test suite, you need PHPUnit. Go to the project root and run:
````
$ phpunit
````
