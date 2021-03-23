<?php

namespace BitterGourd\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

class ConstFetchNodeVisitor extends NodeVisitorAbstract
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

        //$parentNode = $node->getAttribute('parent');

        if ($node instanceof Node\Expr\ConstFetch && $node->getAttribute('converted') != true) {

            if (in_array($node->name->parts[0], ['true', 'false'])) {

                $a = rand(10000, 99999);
                $b = rand(10000, 99999);
                $c = $a - $b;

                if ($node->name->parts[0] == 'false') {
                    $c = $c - 1;
                }

                $code = <<<EOF
            <?php
            \$n=(($a-$b)==$c);
EOF;
                $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
                $ast = $parser->parse(trim($code));
                /** @var Node\Stmt\Expression $newNode */

                return $ast[0]->expr->expr;

            }
        }
        return null;
    }
}
