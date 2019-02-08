<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

final class CategoryTest extends \Tester\TestCase {
  public function testIdentifier() {
    $category = new Category("id");
    $category->identifier = "abc";
    Assert::same("abc", $category->identifier);
  }

  public function testDomain() {
    $category = new Category("id");
    $category->domain = "abc";
    Assert::same("abc", $category->domain);
  }
}

$test = new CategoryTest();
$test->run();
?>