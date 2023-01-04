<?php
/*
|--------------------------------------------------------------------------
| Easy Coding Standard configuration
|--------------------------------------------------------------------------
*/

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $ecsConfig->skip([
        // skip paths with legacy code
        // __DIR__ . '/packages/*/src/Legacy',

        // skip rule completely
        BlankLineAfterOpeningTagFixer::class,
        UnaryOperatorSpacesFixer::class
    ]);

    // A. full sets
    $ecsConfig->sets([SetList::PSR_12]);

    // B. standalone rule
    $ecsConfig->ruleWithConfiguration(ArraySyntaxFixer::class, [
        'syntax' => 'short',
    ]);
    $ecsConfig->rules([
        CastSpacesFixer::class,
        NotOperatorWithSuccessorSpaceFixer::class,
        NoUnusedImportsFixer::class,
        NoExtraBlankLinesFixer::class,
        SingleQuoteFixer::class,
    ]);

    $parameters = $ecsConfig->parameters();
    $parameters->set(Option::CACHE_DIRECTORY, __DIR__ . '/storage/ecs_cache');
};
