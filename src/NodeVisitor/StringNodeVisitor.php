<?php

namespace BitterGourd\NodeVisitor;

use BitterGourd\Common;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class StringNodeVisitor extends NodeVisitorAbstract
{
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Scalar\String_) {
            $parentNode = $node->getAttribute('parent');
            if ($parentNode->getAttribute('child_dont_converted') == true) {
                $node->setAttribute('child_dont_converted', true);
                $node->setAttribute('converted', true);
                return $node;
            }
        }
        return null;
    }

    public function leaveNode(Node $node)
    {

        $parentNode = $node->getAttribute('parent');

        if ($parentNode instanceof Node\Const_) {
            return null;
        }
        if ($parentNode instanceof Node\Stmt\PropertyProperty) {
            return null;
        }

        if ($parentNode instanceof Node\Expr\ArrayItem && $parentNode->getAttribute('parent')->getAttribute('parent') instanceof Node\Stmt\PropertyProperty) {
            return null;
        }

        if ($node instanceof Node\Scalar\String_ && $node->getAttribute('converted') != true) {

            $tNode = $node;
            while (true) {
                $tParentNode = $tNode->getAttribute('parent');
                if ($tParentNode == null) {
                    break;
                }
                /*if ($tParentNode instanceof Node\Stmt\Property && $tParentNode->flags == 9) {
                    return $node;
                }*/
                if ($tParentNode instanceof Node\Stmt\Property) {
                    return $node;
                }
                $tNode = $tParentNode;
            }

            if ($parentNode instanceof Node\Param && $parentNode->getAttribute('parent') instanceof Node\Stmt\Function_) {
                return null;
            }
            if ($parentNode instanceof Node\Param && $parentNode->getAttribute('parent') instanceof Node\Stmt\ClassMethod) {
                return null;
            }

            if ($parentNode instanceof Node\Expr\ArrayItem && $parentNode->key instanceof Node\Scalar\String_ && $parentNode->key->value == $node->value) {
                return null;
            }

            if ($node->getAttribute('kind') != 1) {
                return null;
            }

            if (!is_string($node->value)) {
                return null;
            }

            $newNode = Common::stringNToFuncN($node->value);

            if ($newNode != null) {
                $newNode->setAttribute('converted', true);
                return $newNode;
            }
        }
        return null;
    }
}
