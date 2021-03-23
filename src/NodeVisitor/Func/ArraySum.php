<?php

namespace BitterGourd\NodeVisitor\Func;

use PhpParser\Node;
use PhpParser\ParserFactory;

class ArraySum
{

    function __invoke(Node\Expr\FuncCall $node)
    {
        $code = <<<EOF
            <?php
            call_user_func(function (\$v) {
                \$s = '0';
                foreach (\$v as \$i) {
                    \$l = 0;
                    \$b = explode('.', \$i);
                    if (isset(\$b[1])) {
                        \$l = strlen(\$b[1]);
                    }
                    \$b2 = explode('.', \$s);
                    if (isset(\$b2[1])) {
                        if (\$l < strlen(\$b2[1])) {
                            \$l = strlen(\$b2[1]);
                        }
                    }
                    \$s = bcadd(\$s, \$i, \$l);
                }
                if ((float)\$s > 2147483647 || (float)\$s < -2147483648) {
                    return (float)\$s;
                } else {
                    return (int)\$s;
                }
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