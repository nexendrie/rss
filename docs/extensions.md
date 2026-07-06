Creating extensions
===================

Basics
------

Extensions allow you to use custom elements for channel and items. They have to implement the Nexendrie\Rss\RssExtension interface. The interface defines 4 methods.

**getName** determines the prefix for the extensions' elements names both in keys names for info/data parameter and in the generated XML. E.g. the extension defines element blink and the extension's name is blogChannel, so when adding that element to the channel, the key is named _blogChannel:blink_.

**getNamespace** is the XML namespace for the extension's elements in generated XML.

With **configureChannelOptions** and **configureItemOptions** you define new elements for the channel or items respectively. It uses Symfony's component Options Resolver, make sure to read its documentation to know what is possible. Both methods also get a Nexendrie\Rss\Generator instance so you have to access to configuration.

BaseExtension
-------------

While it is possible to define everything yourself, it is better to extend Nexendrie\Rss\Extensions\BaseExtension which automatically guesses the extension's name from the class name and makes it easier to define your custom elements and create validation for them.

With BaseExtension you can just call method **registerElements** in **configureChannelOptions**/**configureItemOptions** and everything is automatically set up based on rules described below. Values of all public constants defined in your class with prefix _ELEMENT__ (by default, it can be changed with the method's second parameter) are registered as elements. By default, everything is optional; if you want an element to be required, have its returned as an element of array from method **getRequiredElements**.

To restrict an element's value to a certain type, just override method **getElementTypes**. It should return an array, with key being the element's name and value being the allowed type. Possible types are PHP built-in type (see Nexendrie\Rss\Extensions\SimpleElementType), any built-in or user defined class (including enums), simple types can be followed by _[]_ to indicate that an array of that type is accepted. It is also possible to use special types, just check method **getSpecialTypes** to see what is available (you need to use what is in method **getName** of the relevant class as type).

After you have called **registerElements**, you can of course still do things on OptionsResolver if what you want, is not natively supported by BaseExtension. It is only meant to make things easier, not to restrict what you can do.

Examples
--------

See bundled extensions in namespace Nexendrie\Rss\Extension. All of them except RssCore extend BaseExtension.
