<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Nexendrie\Rss\Generator;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @see https://web.resource.org/rss/1.0/modules/dc/
 */
class DublinCore extends BaseExtension
{
    public const string ELEMENT_TITLE = "title";
    public const string ELEMENT_CREATOR = "creator";
    public const string ELEMENT_SUBJECT = "subject";
    public const string ELEMENT_DESCRIPTION = "description";
    public const string ELEMENT_PUBLISHER = "publisher";
    public const string ELEMENT_CONTRIBUTOR = "contributor";
    public const string ELEMENT_DATE = "date";
    public const string ELEMENT_TYPE = "type";
    public const string ELEMENT_FORMAT = "format";
    public const string ELEMENT_IDENTIFIER = "identifier";
    public const string ELEMENT_SOURCE = "source";
    public const string ELEMENT_LANGUAGE = "language";
    public const string ELEMENT_RELATION = "relation";
    public const string ELEMENT_COVERAGE = "coverage";
    public const string ELEMENT_RIGHTS = "rights";

    public function getName(): string
    {
        return "dc";
    }

    public function getNamespace(): string
    {
        return "http://purl.org/dc/elements/1.1/";
    }

    protected function getElementTypes(): array
    {
        return [
            self::ELEMENT_TITLE => "string",
            self::ELEMENT_CREATOR => "string",
            self::ELEMENT_SUBJECT => "string",
            self::ELEMENT_DESCRIPTION => "string",
            self::ELEMENT_PUBLISHER => "string",
            self::ELEMENT_CONTRIBUTOR => "string",
            self::ELEMENT_DATE => "string",
            self::ELEMENT_TYPE => "string",
            self::ELEMENT_FORMAT => "string",
            self::ELEMENT_IDENTIFIER => "string",
            self::ELEMENT_SOURCE => "string",
            self::ELEMENT_LANGUAGE => "string",
            self::ELEMENT_RELATION => "string",
            self::ELEMENT_COVERAGE => "string",
            self::ELEMENT_RIGHTS => "string",
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
