<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Nexendrie\Rss\Generator;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Trackback
 *
 * @author Jakub Konečný
 */
final class Trackback extends BaseExtension
{
    public const string ELEMENT_ABOUT = "about";
    public const string ELEMENT_PING = "ping";

    public function getName(): string
    {
        return "trackback";
    }

    public function getNamespace(): string
    {
        return "http://madskills.com/public/xml/rss/module/trackback/";
    }

    public function configureChannelOptions(OptionsResolver $resolver, Generator $generator): void
    {
    }

    public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void
    {
        $this->registerElements($resolver);
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_ABOUT), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_PING), "string");
    }
}
