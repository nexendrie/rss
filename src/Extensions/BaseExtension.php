<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Nexendrie\Rss\RssExtension;
use Nexendrie\Utils\Constants;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class BaseExtension implements RssExtension {
  use \Nette\SmartObject;

  protected function getElementName(string $baseName): string {
    return $this->getName() . ":" . $baseName;
  }

  protected function registerElements(OptionsResolver $resolver): void {
    $elements = Constants::getConstantsValues(static::class, "ELEMENT_");
    array_walk($elements, function(string $value) use ($resolver): void {
      $resolver->setDefined($this->getElementName($value));
    });
  }
}

?>