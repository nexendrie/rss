<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";

final class RssCoreTest extends \Tester\TestCase {
  public function testGetName() {
    $extension = new RssCore();
    Assert::same("", $extension->getName());
  }

  public function testGetNamespace() {
    $extension = new RssCore();
    Assert::same("", $extension->getNamespace());
  }
}

$test = new RssCoreTest();
$test->run();
?>