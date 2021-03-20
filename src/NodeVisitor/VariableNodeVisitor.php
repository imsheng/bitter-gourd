<?php

namespace BitterGourd\NodeVisitor;

use BitterGourd\Common;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class VariableNodeVisitor extends NodeVisitorAbstract
{
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Expr\Variable) {
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

        if ($node instanceof Node\Expr\Variable && $node->getAttribute('converted') != true) {

            $parentNode = $node->getAttribute('parent');

            if ($parentNode instanceof Node\Stmt\Foreach_) {
                return null;
            }

            if ($parentNode instanceof Node\Stmt\Catch_) {
                return null;
            }

            if ($parentNode instanceof Node\Param) {
                return null;
            }

            if ($parentNode instanceof Node\Expr\ClosureUse) {
                return null;
            }

            if ($parentNode instanceof Node\Stmt\Global_) {
                return null;
            }

            /*            if ($parentNode instanceof Node\Expr\ArrayItem) {
                            if ($parentNode->getAttribute('parent') instanceof Node\Expr\Array_) {
                                if ($parentNode->getAttribute('parent')->getAttribute('parent') instanceof Node\Expr\Assign) {
                                    if ($parentNode->getAttribute('parent')->getAttribute('parent')->var === $parentNode->getAttribute('parent')) {
                                        return $node;
                                    }
                                }
                            }
                        }*/

            /*            if ($parentNode instanceof Node\Expr\MethodCall) {
                            return null;
                        }*/

            if ($parentNode instanceof Node\Expr\PropertyFetch) {
                return null;
            }

            if (!is_string($node->name)) {
                return null;
            }

            if (in_array($node->name, ['GLOBALS', '_SERVER', '_GET', '_POST', '_FILES', '_REQUEST', '_SESSION', '_ENV', '_COOKIE', 'php_errormsg', 'http_response_header', 'argc', 'argv'])) {
                return $node;
            }

            $newNode = Common::stringNToFuncN($node->name);

            if ($newNode != null) {
                $newNode->setAttribute('converted', true);
                $node->setAttribute('converted', true);
                $node->name = $newNode;
            }

            return $node;
        }
    }

}
