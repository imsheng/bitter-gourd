<?php

namespace BitterGourd\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class StaticVarNodeVisitor extends NodeVisitorAbstract
{

    public function enterNode(Node $node) {

        if ($node instanceof Node\Stmt\StaticVar && $node->getAttribute('converted') != true) {

            $node->setAttribute('child_dont_converted', true);
            return $node;
        }
        return null;

    }

}
