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
final class NonNegativeIntegerTest extends \Tester\TestCase
{
    public function testGetName(): void
    {
        $elementType = new NonNegativeInteger();
        Assert::same("non-negative-int", $elementType->getName());
    }

    public function testValidation(): void
    {
        $elementType = new NonNegativeInteger();
        $resolver = new OptionsResolver();
        $resolver->setRequired(["abc",]);
        $resolver->setAllowedTypes("abc", $elementType->getSimpleType()->value);
        $resolver->setAllowedValues("abc", $elementType->getValidator());

        $resolver->resolve(["abc" => 1,]);
        $resolver->resolve(["abc" => 0,]);
        Assert::exception(
            static function () use ($resolver) {
                $resolver->resolve(["abc" => "def",]);
            },
            InvalidOptionsException::class,
            'The option "abc" with value "def" is expected to be of type "int", but is of type "string".'
        );
        Assert::exception(
            static function () use ($resolver) {
                $resolver->resolve(["abc" => -1,]);
            },
            InvalidOptionsException::class,
            'The option "abc" with value -1 is invalid.'
        );
    }
}

$test = new NonNegativeIntegerTest();
$test->run();
