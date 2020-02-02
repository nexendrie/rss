<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Nexendrie\Rss\Generator;
use Nexendrie\Utils\Constants;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Syndication extends BaseExtension {
  public const ELEMENT_UPDATE_PERIOD = "updatePeriod";
  public const ELEMENT_UPDATE_FREQUENCY = "updateFrequency";
  public const ELEMENT_UPDATE_BASE = "updateBase";

  public const UPDATE_PERIOD_HOURLY = "hourly";
  public const UPDATE_PERIOD_DAILY = "daily";
  public const UPDATE_PERIOD_WEAKLY = "weakly";
  public const UPDATE_PERIOD_MONTHLY = "monthly";
  public const UPDATE_PERIOD_YEARLY = "yearly";

  public function getName(): string {
    return "sy";
  }

  public function getNamespace(): string {
    return "http://purl.org/rss/1.0/modules/syndication/";
  }

  public function configureChannelOptions(OptionsResolver $resolver, Generator $generator): void {
    $this->registerElements($resolver);
    $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_UPDATE_PERIOD), "string");
    $resolver->setAllowedValues($this->getElementName(self::ELEMENT_UPDATE_PERIOD), function(string $value) {
      return (in_array($value, Constants::getConstantsValues(self::class, "UPDATE_PERIOD_"), true));
    });
    $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_UPDATE_FREQUENCY), "int");
    $resolver->setAllowedValues($this->getElementName(self::ELEMENT_UPDATE_FREQUENCY), function(int $value) {
      return ($value >= 1);
    });
    $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_UPDATE_BASE), "string");
  }

  public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void {
  }
}

?>