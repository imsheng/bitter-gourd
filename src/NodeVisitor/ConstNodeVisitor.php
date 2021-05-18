<?php

namespace BitterGourd\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ConstNodeVisitor extends NodeVisitorAbstract
{

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Const_) {
            $node->setAttribute('child_dont_converted', true);
        }
    }

}
