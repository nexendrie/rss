<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Nexendrie\Rss\IRssExtension;
use Nexendrie\Utils\Constants;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class BaseExtension implements IRssExtension {
  use \Nette\SmartObject;

  protected function getElementName(string $baseName): string {
    return $this->getName() . ":" . $baseName;
  }

  protected function registerElements(OptionsResolver $resolver): void {
    $elements = Constants::getConstantsValues(static::class, "ELEMENT_");
    array_walk($elements, function(string $value) use ($resolver) {
      $resolver->setDefined($this->getElementName($value));
    });
  }
}

?>