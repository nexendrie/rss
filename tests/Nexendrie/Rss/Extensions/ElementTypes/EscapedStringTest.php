<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions\ElementTypes;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use Nexendrie\Rss\XmlElement;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[TestSuite("EscapedString")]
#[Group("elementTypes")]
final class EscapedStringTest extends \MyTester\TestCase
{
    public function testGetName(): void
    {
        $elementType = new EscapedString();
        $this->assertSame("escaped-string", $elementType->getName());
    }

    public function testValidation(): void
    {
        $elementType = new EscapedString();
        $resolver = new OptionsResolver();
        $resolver->setRequired(["abc",]);
        $resolver->setAllowedTypes("abc", $elementType->getSimpleType()->value);
        $resolver->setAllowedValues("abc", $elementType->getValidator());

        $resolver->resolve(["abc" => "test",]);
        $this->assertThrowsException(
            static function () use ($resolver) {
                $resolver->resolve(["abc" => 123,]);
            },
            InvalidOptionsException::class,
            'The option "abc" with value 123 is expected to be of type "string", but is of type "int".'
        );
    }

    public function testNormalization(): void
    {
        $elementType = new EscapedString();
        $resolver = new OptionsResolver();
        $resolver->setRequired(["abc",]);
        $resolver->setAllowedTypes("abc", $elementType->getSimpleType()->value);
        $resolver->setAllowedValues("abc", $elementType->getValidator());
        $resolver->setNormalizer(
            "abc",
            fn (Options $options, $elementValue) => $elementType->getNormalizer()("abc", $elementValue, null)
        );

        $result = $resolver->resolve(["abc" => "test",]);
        $element = $result["abc"];
        $this->assertType(XmlElement::class, $element);
        /** @var XmlElement $element */
        $this->assertSame("abc", $element->name);
        $this->assertSame("test", $element->value);
        $this->assertNull($element->namespaceUri);
    }
}
