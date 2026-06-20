<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions\ElementTypes;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[TestSuite("PositiveInteger")]
#[Group("elementTypes")]
final class PositiveIntegerTest extends \MyTester\TestCase
{
    public function testGetName(): void
    {
        $elementType = new PositiveInteger();
        $this->assertSame("positive-int", $elementType->getName());
    }

    public function testValidation(): void
    {
        $elementType = new PositiveInteger();
        $resolver = new OptionsResolver();
        $resolver->setRequired(["abc",]);
        $resolver->setAllowedTypes("abc", $elementType->getSimpleType()->value);
        $resolver->setAllowedValues("abc", $elementType->getValidator());

        $resolver->resolve(["abc" => 1,]);
        $this->assertThrowsException(
            static function () use ($resolver) {
                $resolver->resolve(["abc" => "def",]);
            },
            InvalidOptionsException::class,
            'The option "abc" with value "def" is expected to be of type "int", but is of type "string".'
        );
        $this->assertThrowsException(
            static function () use ($resolver) {
                $resolver->resolve(["abc" => 0,]);
            },
            InvalidOptionsException::class,
            'The option "abc" with value 0 is invalid.'
        );
    }
}
