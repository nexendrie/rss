<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Rss Channel Item
 *
 * @author Jakub Konečný
 */
final class RssChannelItem {
  use \Nette\SmartObject;

  private array $data;

  public function __construct(array $data) {
    $this->data = $data;
  }

  private function configureOptions(OptionsResolver $resolver, Generator $generator): void {
    foreach($generator->extensions as $extension) {
      $extension->configureItemOptions($resolver, $generator);
    }
  }

  public function toXml(\SimpleXMLElement &$element, Generator $generator): void {
    $resolver = new OptionsResolver();
    $this->configureOptions($resolver, $generator);
    $data = $resolver->resolve($this->data);
    foreach($data as $key => $value) {
      if($value === "") {
        continue;
      }
      if(!$value instanceof IXmlConvertible) {
        $value = new GenericElement($key, $value);
      }
      $value->appendToXml($element);
    }
  }
}
?>