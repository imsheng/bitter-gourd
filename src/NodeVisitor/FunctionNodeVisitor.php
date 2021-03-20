<?php

namespace BitterGourd\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Doctrine\Inflector\InflectorFactory;

class FunctionNodeVisitor extends NodeVisitorAbstract
{

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Expr\FuncCall && $node->getAttribute('converted') != true) {
            $inflector = InflectorFactory::create()->build();

            $node->setAttribute('parent', null);

            if (!isset($node->name->parts) || !is_string($node->name->parts[0])) {
                return $node;
            }

            $fClassStr = sprintf('BitterGourd\NodeVisitor\Func\%s', $inflector->classify($node->name->parts[0]));

            if ($node->name instanceof Node\Name && class_exists($fClassStr)) {
                $fClass = new $fClassStr();
                return $fClass($node);
            }
        }
        return $node;
    }

}
