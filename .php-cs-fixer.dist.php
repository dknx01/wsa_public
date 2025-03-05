<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

if (!file_exists(__DIR__.'/src')) {
    exit(0);
}

$fileHeaderComment = <<<'EOF'
This file is part of the Weltsozialamt website.
(c) dknx01/wsa_public
EOF;

return (new PhpCsFixer\Config())
    // @see https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/pull/7777
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRules([
        '@PHPUnit75Migration:risky' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'header_comment' => ['header' => $fileHeaderComment],
        'nullable_type_declaration' => true,
        // TODO: Remove once the "compiler_optimized" set includes "sprintf"
        'native_function_invocation' => ['include' => ['@compiler_optimized', 'sprintf'], 'scope' => 'namespaced', 'strict' => true],
        'trailing_comma_in_multiline' => ['elements' => ['arrays', 'match', 'parameters']],
    ])
    ->setRiskyAllowed(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in([
                __DIR__.'/src',
                __DIR__.'/migrations',
                __DIR__.'/tests',
            ])
            ->append([__FILE__])
    )
    ->setCacheFile('.php-cs-fixer.cache')
;
