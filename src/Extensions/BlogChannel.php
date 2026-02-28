<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Nexendrie\Rss\Generator;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @see http://backend.userland.com/blogChannelModule
 */
final class BlogChannel extends BaseExtension
{
    public const string ELEMENT_BLOG_ROLL = "blogRoll";
    public const string ELEMENT_MY_SUBSCRIPTIONS = "mySubscriptions";
    public const string ELEMENT_BLINK = "blink";
    public const string ELEMENT_CHANGES = "changes";

    public function getNamespace(): string
    {
        return "http://backend.userland.com/blogChannelModule";
    }

    protected function getElementTypes(): array
    {
        return [
            self::ELEMENT_BLOG_ROLL => "string",
            self::ELEMENT_MY_SUBSCRIPTIONS => "string",
            self::ELEMENT_BLINK => "string",
            self::ELEMENT_CHANGES => "string",
        ];
    }

    public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void
    {
        $this->registerElements($resolver);
    }
}
