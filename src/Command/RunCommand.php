<?php

namespace BitterGourd\Command;

use BitterGourd\NodeVisitor\ArrayNodeVisitor;
use BitterGourd\NodeVisitor\ConstFetchNodeVisitor;
use BitterGourd\NodeVisitor\ConstNodeVisitor;
use BitterGourd\NodeVisitor\ForeachNodeVisitor;
use BitterGourd\NodeVisitor\ForNodeVisitor;
use BitterGourd\NodeVisitor\FunctionNodeVisitor;
use BitterGourd\NodeVisitor\IfNodeVisitor;
use BitterGourd\NodeVisitor\MethodCallNodeVisitor;
use BitterGourd\NodeVisitor\ParamNodeVisitor;
use BitterGourd\NodeVisitor\PropertyFetchNodeVisitor;
use BitterGourd\NodeVisitor\StaticVarNodeVisitor;
use BitterGourd\NodeVisitor\StringNodeVisitor;
use BitterGourd\NodeVisitor\SwitchNodeVisitor;
use BitterGourd\NodeVisitor\VariableNodeVisitor;
use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NodeConnectingVisitor;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use PhpParser\PrettyPrinter;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class RunCommand extends Command
{

    protected static $defaultName = 'run';

    protected function configure()
    {
        $this
            ->setDescription('Describe args behaviors')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('path', 'p', InputArgument::OPTIONAL, 'Select a directory or file path.'),
                    new InputOption('loop', 'l', InputArgument::OPTIONAL, 'Loop. The default value is 1 (max:3)'),
                    new InputOption('test', 't', InputArgument::REQUIRED, 'Test. Create the test file'),
                    new InputOption('strip', 's', InputArgument::REQUIRED, 'Clear comments and whitespace'),
                ])
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filesystem = new Filesystem();
        $finder = new Finder();

        $path = $input->getOption('path');
        $loop = abs(intval($input->getOption('loop') ?? 1));
        if ($loop > 3) {
            $loop = 3;
        }
        $isTest = $input->getOption('test');
        $phpFiles = [];

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Have you backed up your files or directories? (y):', true);

        if (!$isTest && !$helper->ask($input, $output, $question)) {
            return 0;
        }

        if (!$filesystem->exists($path)) {
            $output->writeln('Path error!');
            return 0;
        }

        if (is_dir($path)) {
            $phpFiles = iterator_to_array($finder->in($path)->name('*.php')->files());
        }

        if (!is_dir($path)) {
            $phpFiles = [$path];
        }

        $isStrip = $input->getOption('strip');

        foreach ($phpFiles as $phpFile) {
            $output->writeln($phpFile);
            $code = $isStrip ? php_strip_whitespace($phpFile) : trim(file_get_contents($phpFile));

            for ($i = 0; $i < $loop; $i++) {
                $code = $this->obscure($code);
            }

            if ($isTest) {
                $parsePath = pathinfo($phpFile);
                $phpFile = sprintf('%s%s%s-test.%s', $parsePath['dirname'], DIRECTORY_SEPARATOR, $parsePath['filename'], $parsePath['extension']);
            }
            file_put_contents($phpFile, $code);
        }

        $output->writeln('done.');
        return 0;
    }

    private function obscure($code)
    {

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        try {
            $ast = $parser->parse($code);
        } catch (Error $error) {
            return $code;
        }

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NodeConnectingVisitor());
        $traverser->addVisitor(new ConstNodeVisitor());
        $traverser->addVisitor(new StaticVarNodeVisitor());
        $traverser->addVisitor(new ParamNodeVisitor());
        $traverser->addVisitor(new ForeachNodeVisitor());
        $traverser->addVisitor(new ForNodeVisitor());
        //$traverser->addVisitor(new IfNodeVisitor());
        $traverser->addVisitor(new ArrayNodeVisitor());
        //$traverser->addVisitor(new SwitchNodeVisitor());
        $traverser->addVisitor(new MethodCallNodeVisitor());
        $traverser->addVisitor(new PropertyFetchNodeVisitor());
        $traverser->addVisitor(new FunctionNodeVisitor());
        $traverser->addVisitor(new StringNodeVisitor());
        $traverser->addVisitor(new ConstFetchNodeVisitor());
        $traverser->addVisitor(new VariableNodeVisitor());
        ////x$traverser->addVisitor(new LineNodeVisitor());
        $ast = $traverser->traverse($ast);

        $prettyPrinter = new PrettyPrinter\Standard;
        return $prettyPrinter->prettyPrintFile($ast);
    }


}
