<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Nexendrie\Rss\Generator;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @see https://www.rssboard.org/comment-api
 */
final class CommentApi extends BaseExtension
{
    public const string ELEMENT_COMMENT = "comment";
    public const string ELEMENT_COMMENT_RSS = "commentRss";

    public function getName(): string
    {
        return "wfw";
    }

    public function getNamespace(): string
    {
        return "http://wellformedweb.org/CommentAPI/";
    }

    protected function getElementTypes(): array
    {
        return [
            self::ELEMENT_COMMENT => "string",
            self::ELEMENT_COMMENT_RSS => "string",
        ];
    }

    public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void
    {
        $this->registerElements($resolver);
    }
}
