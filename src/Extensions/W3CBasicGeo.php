<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Nexendrie\Rss\Generator;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Trackback
 *
 * @author Jakub Konečný
 * @see https://www.w3.org/2003/01/geo/
 */
final class W3CBasicGeo extends BaseExtension
{
    public const string ELEMENT_LATITUDE = "lat";
    public const string ELEMENT_LONGITUDE = "lon";
    public const string ELEMENT_ALTITUDE = "alt";

    public function getName(): string
    {
        return "geo";
    }

    public function getNamespace(): string
    {
        return "http://www.w3.org/2003/01/geo/wgs84_pos#";
    }

    protected function getElementTypes(): array
    {
        return [
            self::ELEMENT_LATITUDE => "float",
            self::ELEMENT_LONGITUDE => "float",
            self::ELEMENT_ALTITUDE => "float",
        ];
    }

    public function configureChannelOptions(OptionsResolver $resolver, Generator $generator): void
    {
        $this->registerElements($resolver);
    }

    public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void
    {
        $this->registerElements($resolver);
    }
}
