<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Nexendrie\Rss\Generator;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Trackback
 *
 * @author Jakub Konečný
 * @see https://www.rssboard.org/trackback
 */
final class Trackback extends BaseExtension
{
    public const string ELEMENT_ABOUT = "about";
    public const string ELEMENT_PING = "ping";

    public function getNamespace(): string
    {
        return "http://madskills.com/public/xml/rss/module/trackback/";
    }

    protected function getElementTypes(): array
    {
        return [
            self::ELEMENT_ABOUT => "string",
            self::ELEMENT_PING => "string",
        ];
    }

    public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void
    {
        $this->registerElements($resolver);
    }
}
