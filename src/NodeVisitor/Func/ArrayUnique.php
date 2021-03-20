<?php

namespace BitterGourd\NodeVisitor\Func;

use PhpParser\Node;
use PhpParser\ParserFactory;

class ArrayUnique
{

    function __invoke(Node\Expr\FuncCall $node)
    {
        $code = <<<EOF
            <?php
            call_user_func(function (\$v) {
                \$r = [];
                foreach (\$v as \$i) {
                    if (array_search(\$i, \$r) === false) {
                        \$r[] = \$i;
                    }
                }
                return \$r;
            }, null);
EOF;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $ast = $parser->parse(trim($code));

        /** @var Node\Stmt\Expression $newNode */
        $newNode = $ast[0];

        $newNode->expr->args[1] = $node->args[0];

        $newNode->expr->setAttribute('converted', true);

        return $newNode->expr;
    }

}