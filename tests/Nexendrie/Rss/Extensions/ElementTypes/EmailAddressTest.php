<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions\ElementTypes;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tester\Assert;

require __DIR__ . "/../../../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class EmailAddressTest extends \Tester\TestCase
{
    public function testGetName(): void
    {
        $elementType = new EmailAddress();
        Assert::same("email", $elementType->getName());
    }

    public function testValidation(): void
    {
        $elementType = new EmailAddress();
        $resolver = new OptionsResolver();
        $resolver->setRequired(["abc",]);
        $resolver->setAllowedTypes("abc", $elementType->getSimpleType()->value);
        $resolver->setAllowedValues("abc", $elementType->getValidator());

        $resolver->resolve(["abc" => "abc@test.localhost",]);
        $resolver->resolve(["abc" => "abc+def@test.localhost",]);
        $resolver->resolve(["abc" => "abc.def@test.localhost",]);
        $resolver->resolve(["abc" => "abc-def@test.localhost",]);
        $resolver->resolve(["abc" => "abc@test.localhost (Test Tester)",]);
        $resolver->resolve(["abc" => "abc@test.localhost (Test Testovič)",]);
        Assert::exception(
            static function () use ($resolver) {
                $resolver->resolve(["abc" => 123,]);
            },
            InvalidOptionsException::class,
            'The option "abc" with value 123 is expected to be of type "string", but is of type "int".'
        );
        Assert::exception(
            static function () use ($resolver) {
                $resolver->resolve(["abc" => "test",]);
            },
            InvalidOptionsException::class,
            'The option "abc" with value "test" is invalid.'
        );
    }
}

$test = new EmailAddressTest();
$test->run();
