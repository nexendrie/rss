Rss
===

This package helps you generate Rss channels.

Links
-----

Primary repository: https://gitlab.com/nexendrie/rss
GitHub repository: https://github.com/nexendrie/rss
Packagist: https://packagist.org/packages/nexendrie/rss

Installation
------------
The best way to install it is via Composer. Just add **nexendrie/rss** to your dependencies.

Usage
-----

Create new instance of \Nexendrie\Rss\Generator and add data source for channel's items. It has to return \Nexendrie\Rss\Collection which is a collection of \Nexendrie\Rss\RssChannelItem. Then you can call method generate with array that contains info about the channel. The basic minimum is title and description.

Example:

```php
<?php
declare(strict_types=1);

use DateTime;
use Nexendrie\Rss\Generator;
use Nexendrie\Rss\RssChannelItem,
use Nexendrie\Rss\Collection;

$generator = new Generator();
$generator->dataSource = function() use($generator) {
  $items = new Collection();
  $items[] = new RssChannelItem([
    "title" => "Item 1", "description" => "Item 1 description", "link" => "https://nexendrie.cz/item1", "pubDate" => new DateTime(),
  ]);
  return $items;
};
$info = [
  "title" => "Nexendrie RSS", "link" => "https://nexendrie.cz/rss", "description" => "News for package nexendrie/rss",
];
$result = $generator->generate($info);
?>
```

Method generate returns plain text that can be shown with echo.

Advanced usage
--------------

By default, the generator shows all times in the channel in the recommended format but it is possible to change it. Just use:

```php
<?php
declare(strict_types=1);

$generator = new Nexendrie\Rss\Generator();
$generator->dateTimeFormat = "your preferred format";
?>
```

By default, only first 150 characters of items' description are printed (remember, it is supposed to be synopsis). You can change the length limit like this:

```php
<?php
declare(strict_types=1);

$generator = new Nexendrie\Rss\Generator();
$generator->shortenDescription = 500;
?>
```

or completely disable it by setting the property to 0.

You can also change lastBuildDate for channel by adding key lastBuildDate to the info parameter. It accepts callback that returns a DateTime object. Default value is current time. The same applies to pubDate but that has no default value.

It is also possible to use custom template for RSS Channel. Just use:

```php
<?php
declare(strict_types=1);

$generator = new Nexendrie\Rss\Generator();
$generator->template = "/path/to/your/template.xml";
?>
```

You can also set language, copyright, managingEditor, webMaster, ttl, rating, skipDays, skipHours, image, cloud and textInput for channel by adding those keys to the info parameter. skipHours has to be an array of integers between 0 and 23, skipDays has to be an array of enum Nexendrie\Rss\Extensions\RssCore\SkipDay. Image, cloud and textInput are entered as an instance of Nexendrie\Rss\Image, Nexendrie\Rss\Cloud or Nexendrie\Rss\TextInput respectively. Language has to be an enum Nexendrie\Rss\Extensions\RssCore\Iso639Language or Nexendrie\Rss\Extensions\RssCore\RssLanguage.

We add generator and docs to channel but you can change their values by setting property of the same name on Generator. If you do not want to have them in your channel at all, set their values to an empty string.

It is possible to set keys author, comments and guid of parameter data passed to Item's constructor which will be added to the generated xml. You can also add source to items, just add key source with an instance of Nexendrie\Rss\Source (url is mandatory, without it source is not added to xml).

Both channel and individual items can have any number of categories. Category is represented by class Nexendrie\Rss\Category and has an identifier and domain (the latter is optional). You can add categories to channel or item via key categories in info/data parameter as and object of type Nexendrie\Rss\CategoriesCollection which behaves like an array (you can add new elements and remove/modify them).

It is possible to add multiple enclosures into an item, just add an instance of Nexendrie\Rss\Enclosure as value for key enclosure to data parameter.

Custom elements and attributes
------------------------------

It is possible to insert also other elements and attributes to the channel and single items. It is rather simple. Firstly you need to create an extension which defines the additional things for channel and items and register the extension to the generator. Then you can add those keys to info parameter when generating the channel or data parameter when creating a new item.

Extensions have to implement the Nexendrie\Rss\RssExtension interface and are registered this way:

```php
<?php
declare(strict_types=1);

$generator = new Nexendrie\Rss\Generator();
$generator->extensions[] = new classname();
?>
```

.

A few extensions are a part of this package (in namespace Nexendrie\Rss\Extension) and also all core elements and attributes are defined in an extension.

For details about creating extensions, read [this](extensions.md).

Nette applications
------------------

The package contains extension for Nette DI container which adds the generator. It allows you to change some parameters of generator. Example (with default values):

```yaml
extensions:
    rss: Nexendrie\Rss\Bridges\NetteDI\RssExtension
rss:
    shortenDescription: 150
    dateTimeFormat: "r"
    template: "/path/to/default/template.xml"
```

If you add any class implementing the Nexendrie\Rss\RssExtension interface to the container, they will be automatically registered to the generator. Alternatively, you can register them through the DIC extension:

```yaml
rss:
    extensions:
        - classname1
        - classname2
```

If you do not need to do anything with the result after generating, you can use method **response** instead of **generate** to get a response to send from your presenter:

```php
<?php
declare(strict_types=1);

$response = $this->generator->response([...]);
$this->sendResponse($response);
?>
```

.
