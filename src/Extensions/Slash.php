<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Nexendrie\Rss\Generator;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Slash
 *
 * @author Jakub Konečný
 */
final class Slash extends BaseExtension {
  public const ELEMENT_SECTION = "section";
  public const ELEMENT_DEPARTMENT = "department";
  public const ELEMENT_COMMENTS = "comments";
  public const ELEMENT_HIT_PARADE = "hit_parade";

  public function getName(): string {
    return "slash";
  }

  public function getNamespace(): string {
    return "http://purl.org/rss/1.0/modules/slash/";
  }

  public function configureChannelOptions(OptionsResolver $resolver, Generator $generator): void {
  }

  public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void {
    $this->registerElements($resolver);
    $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_SECTION), "string");
    $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_DEPARTMENT), "string");
    $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_COMMENTS), "int");
    $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_HIT_PARADE), "string");
    $resolver->setAllowedValues($this->getElementName(self::ELEMENT_HIT_PARADE), function(string $value) {
      return (preg_match('#^\d(,\d)*$#', $value) === 1);
    });
  }
}

?>