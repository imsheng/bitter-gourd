<?php

namespace BitterGourd\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ArrayNodeVisitor extends NodeVisitorAbstract
{
    public function enterNode(Node $node)
    {

        $parentNode = $node->getAttribute('parent');

        if ($node instanceof Node\Expr\Array_ || $node instanceof Node\Expr\ArrayItem) {
            if ($parentNode->getAttribute('child_dont_converted') == true) {
                $node->setAttribute('child_dont_converted', true);
                $node->setAttribute('converted', true);
                return $node;
            }
        }
        return null;
    }

}
