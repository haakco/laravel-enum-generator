<?php

$finder = PhpCsFixer\Finder::create()
  ->exclude('bootstrap/cache')
  ->exclude('node_modules')
  ->exclude('storage')
  ->exclude('*.blade.php')
  ->in(__DIR__)
  ->notName('*.blade.php')
  ->notName('.phpstorm.meta.php')
  ->notName('_ide_*.php')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
      'ordered_imports' => ['sort_algorithm' => 'alpha'],
      'trailing_comma_in_multiline' => true,
      'method_argument_space' => [
        'on_multiline' => 'ensure_fully_multiline',
      ],
    ])
    ->setFinder($finder);
