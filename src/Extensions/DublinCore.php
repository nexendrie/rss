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

    public function configureChannelOptions(OptionsResolver $resolver, Generator $generator): void
    {
        $this->registerElements($resolver);
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_TITLE), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_CREATOR), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_SUBJECT), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_DESCRIPTION), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_PUBLISHER), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_CONTRIBUTOR), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_DATE), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_TYPE), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_FORMAT), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_IDENTIFIER), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_SOURCE), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_LANGUAGE), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_RELATION), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_COVERAGE), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_RIGHTS), "string");
    }

    public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void
    {
        $this->registerElements($resolver);
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_TITLE), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_CREATOR), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_SUBJECT), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_DESCRIPTION), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_PUBLISHER), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_CONTRIBUTOR), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_DATE), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_TYPE), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_FORMAT), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_IDENTIFIER), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_SOURCE), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_LANGUAGE), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_RELATION), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_COVERAGE), "string");
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_RIGHTS), "string");
    }
}
