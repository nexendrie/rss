<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions\ElementTypes;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[TestSuite("NonPositiveInteger")]
#[Group("elementTypes")]
final class NonPositiveIntegerTest extends \MyTester\TestCase
{
    public function testGetName(): void
    {
        $elementType = new NonPositiveInteger();
        $this->assertSame("non-positive-int", $elementType->getName());
    }

    public function testValidation(): void
    {
        $elementType = new NonPositiveInteger();
        $resolver = new OptionsResolver();
        $resolver->setRequired(["abc",]);
        $resolver->setAllowedTypes("abc", $elementType->getSimpleType()->value);
        $resolver->setAllowedValues("abc", $elementType->getValidator());

        $resolver->resolve(["abc" => -1,]);
        $resolver->resolve(["abc" => 0,]);
        $this->assertThrowsException(
            static function () use ($resolver) {
                $resolver->resolve(["abc" => "def",]);
            },
            InvalidOptionsException::class,
            'The option "abc" with value "def" is expected to be of type "int", but is of type "string".'
        );
        $this->assertThrowsException(
            static function () use ($resolver) {
                $resolver->resolve(["abc" => 1,]);
            },
            InvalidOptionsException::class,
            'The option "abc" with value 1 is invalid.'
        );
    }
}
