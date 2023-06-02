<?php

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\ViewHelpers;

use League\CommonMark\GithubFlavoredMarkdownConverter;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

class ParsedownViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('text', 'string', 'The text with markdown syntax to be parsed.', false);
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        $text = $arguments['text'] ?? $renderChildrenClosure();
        $converter = new GithubFlavoredMarkdownConverter([
            'renderer' => [
                'block_separator' => "\n",
            ],
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
        return $converter->convert($text);
    }
}
