# TwigView plugin for CakePHP #

[![Build Status](https://travis-ci.org/WyriHaximus/TwigView.png)](https://travis-ci.org/WyriHaximus/TwigView)
[![Latest Stable Version](https://poser.pugx.org/WyriHaximus/Twig-View/v/stable.png)](https://packagist.org/packages/WyriHaximus/Twig-View)
[![Total Downloads](https://poser.pugx.org/WyriHaximus/Twig-View/downloads.png)](https://packagist.org/packages/WyriHaximus/Twig-View)
[![Code Coverage](https://scrutinizer-ci.com/g/WyriHaximus/TwigView/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/WyriHaximus/TwigView/?branch=master)
[![License](https://poser.pugx.org/wyrihaximus/Twig-View/license.png)](https://packagist.org/packages/wyrihaximus/Twig-View)
[![PHP 7 ready](http://php7ready.timesplinter.ch/WyriHaximus/TwigView/badge.svg)](https://travis-ci.org/WyriHaximus/TwigView)

This plugin for version 3 the [CakePHP Framework](http://cakephp.org) allows you to use the [Twig Templating Language](http://twig.sensiolabs.org) for your views.

In addition to enabling the use of most of Twig's features, the plugin is tightly integrated with the CakePHP view renderer giving you full access to helpers, objects and elements.

## Installation ##

To install via [Composer](http://getcomposer.org/), use the command below, it will automatically detect the latest version and bind it with `~`.

```bash
composer require wyrihaximus/twig-view
```

## Configuration ##

### Load Plugin
Add the following to your `config/bootstrap.php` to load the plugin.
```php
Plugin::load('WyriHaximus/TwigView', [
    'bootstrap' => true,
]);
```

### Use View class
Instead of extending from the `View` let `AppView` extend `TwigView`:

```php
namespace App\View;

use WyriHaximus\TwigView\View\TwigView;

class AppView extends TwigView
{
}
```

## Quick Start

### Load Helpers
In `Controller/AppController` load your helpers:

```PHP
public $helpers = ['Html', 'Form']; // And more
```

Or in your AppView file :

```PHP
public function initialize()
{
    parent::initialize();

    $this->loadHelper('Html');
    $this->loadHelper('Form');
}
```

### Layout
Replace `Template/Layout/default.ctp` by this `Layout/default.tpl`

``` twig
<!DOCTYPE html>
<html>
<head>
    {{ Html.charset()|raw }}

    <title>
        {{ __('myTwigExample') }}
        {{ _view.fetch('title')|raw }}
    </title>

    {{ Html.meta('icon')|raw }}

    {{ Html.css('default.app.css')|raw }}
    {{ Html.script('app')|raw }}

    {{ _view.fetch('meta')|raw }}
    {{ _view.fetch('css')|raw }}
    {{ _view.fetch('script')|raw }}
</head>
<body>
    <header>
        {{ _view.fetch('header')|raw }}
    </header>

    {{ Flash.render()|raw }}

    <section>

        <h1>{{ _view.fetch('title')|raw }}</h1>

        {{ _view.fetch('content')|raw }}
    </section>

    <footer>
        {{ _view.fetch('footer')|raw }}
    </footer>
</body>
</html>
```

### Template View
Create a template, for exemple `Template/Users/index.tpl` like this
```Twig
{{ _view.assign('title', __("I'm title")) }}

{{ _view.start('header') }}
    <p>I'm header</p>
{{ _view.end() }}

{{ _view.start('footer') }}
    <p>I'm footer</p>
{{ _view.end() }}

<p>I'm content</p>
```

## Usage

### Use `$this`
With twig `$this` is replaced by `_view`

For example, without using Twig writing
```php
<?= $this->fetch('content') ?>
```
But with Twig
```twig
{{ _view.fetch('content')|raw }}
```
### Helpers

Any helper you defined in your controller.

```php
public $helpers = ['Html', 'Form']; // ...
```

Can be access by their CamelCase name, for example creating a form using the `FormHelper`:

```twig
{{ Html.link('Edit user', {'controller':'Users', 'action': 'edit' ~ '/' ~ user.id}, {'class':'myclass'})|raw }}
```

### Elements
Basic
```Twig
{% element 'Element' %}
```
With variables or options
```Twig
{% element 'Plugin.Element' {
    dataName: 'dataValue'
} {
    optionName: 'optionValue'
} %}
```

### Cells

Store in context then echo it
```twig
{% cell cellObject = 'Plugin.Cell' {
    dataName: 'dataValue'
} {
    optionName: 'optionValue'
} %}

{{ cellObject|raw }}
```

Fetch and directly echo it
```twig
{% cell 'Plugin.Cell' {
    dataName: 'dataValue'
} {
    optionName: 'optionValue'
} %}
```

### Extends
If i want extend to `Common/demo.tpl`
```twig
<div id="sidebar">
    {% block sidebar %}{% endblock %}
</div>

<div id="content">
    {% block body %}{% endblock %}
</div>
```
We can write in a view
```twig
{% extends 'Common/demo' %}

{% block sidebar %}
    <ul>
        <li>Item 1</li>
        <li>Item 2</li>
        <li>Item 3</li>
    </ul>
{% endblock %}

{% block body %}

    {{ _view.assign('title', __("I'm title")) }}

    {{ _view.start('header') }}
        <p>I'm header</p>
    {{ _view.end() }}

    {{ _view.start('footer') }}
        <p>I'm footer</p>
    {{ _view.end() }}

    <p>I'm content</p>
{% endblock %}
```

**Note : the block `body` is required, it's equivalent to `<?= $this->fetch('content') ?>`**

### Filters
* `debug` maps to [`debug`](http://book.cakephp.org/3.0/en/development/debugging.html#basic-debugging)
* `pr` maps to `pr`
* `low` maps to [`low`](http://php.net/low)
* `up` maps to [`up`](http://php.net/up)
* `env` maps to [`env`](http://php.net/env)
* `count` maps to [`count`](http://php.net/count)
* `pluralize` maps to [`Cake\Utility\Inflector::pluralize`](http://book.cakephp.org/3.0/en/core-libraries/inflector.html#Cake\\Utility\\Inflector::pluralize)
* `singularize` maps to [`Cake\Utility\Inflector::singularize`](http://book.cakephp.org/3.0/en/core-libraries/inflector.html#Cake\\Utility\\Inflector::singularize)
* `camelize` maps to [`Cake\Utility\Inflector::camelize`](http://book.cakephp.org/3.0/en/core-libraries/inflector.html#Cake\\Utility\\Inflector::camelize)
* `underscore` maps to [`Cake\Utility\Inflector::underscore`](http://book.cakephp.org/3.0/en/core-libraries/inflector.html#Cake\\Utility\\Inflector::underscore)
* `humanize` maps to [`Cake\Utility\Inflector::humanize`](http://book.cakephp.org/3.0/en/core-libraries/inflector.html#Cake\\Utility\\Inflector::humanize)
* `tableize` maps to [`Cake\Utility\Inflector::tableize`](http://book.cakephp.org/3.0/en/core-libraries/inflector.html#Cake\\Utility\\Inflector::tableize)
* `classify` maps to [`Cake\Utility\Inflector::classify`](http://book.cakephp.org/3.0/en/core-libraries/inflector.html#Cake\\Utility\\Inflector::classify)
* `variable` maps to [`Cake\Utility\Inflector::variable`](http://book.cakephp.org/3.0/en/core-libraries/inflector.html#Cake\\Utility\\Inflector::variable)
* `slug` maps to [`Cake\Utility\Inflector::slug`](http://book.cakephp.org/3.0/en/core-libraries/inflector.html#Cake\\Utility\\Inflector::slug)
* `toReadableSize` maps to [`Cake\I18n\Number::toReadableSize`](http://book.cakephp.org/3.0/en/core-libraries/number.html#Cake\\I18n\\Number::toReadableSize)
* `fromReadableSize` maps to [`Cake\I18n\Number::fromReadableSize`](http://book.cakephp.org/3.0/en/core-libraries/number.html#Cake\\I18n\\Number::fromReadableSize)
* `toPercentage` maps to [`Cake\I18n\Number::toPercentage`](http://book.cakephp.org/3.0/en/core-libraries/number.html#Cake\\I18n\\Number::toPercentage)
* `format` maps to [`Cake\I18n\Number::format`](http://book.cakephp.org/3.0/en/core-libraries/number.html#Cake\\I18n\\Number::format)
* `formatDelta` maps to [`Cake\I18n\Number::formatDelta`](http://book.cakephp.org/3.0/en/core-libraries/number.html#Cake\\I18n\\Number::formatDelta)
* `currency` maps to [`Cake\I18n\Number::currency`](http://book.cakephp.org/3.0/en/core-libraries/number.html#Cake\\I18n\\Number::currency)
* `substr` maps to [`substr`](http://php.net/substr)
* `tokenize` maps to [`Cake\Utility\String::tokenize`](http://book.cakephp.org/3.0/en/core-libraries/string.html#Cake\\Utility\\String::tokenize)
* `insert` maps to [`Cake\Utility\String::insert`](http://book.cakephp.org/3.0/en/core-libraries/string.html#Cake\\Utility\\String::insert)
* `cleanInsert` maps to [`Cake\Utility\String::cleanInsert`](http://book.cakephp.org/3.0/en/core-libraries/string.html#Cake\\Utility\\String::cleanInsert)
* `wrap` maps to [`Cake\Utility\String::wrap`](http://book.cakephp.org/3.0/en/core-libraries/string.html#Cake\\Utility\\String::wrap)
* `wordWrap` maps to [`Cake\Utility\String::wordWrap`](http://book.cakephp.org/3.0/en/core-libraries/string.html#Cake\\Utility\\String::wordWrap)
* `highlight` maps to [`Cake\Utility\String::highlight`](http://book.cakephp.org/3.0/en/core-libraries/string.html#Cake\\Utility\\String::highlight)
* `tail` maps to [`Cake\Utility\String::tail`](http://book.cakephp.org/3.0/en/core-libraries/string.html#Cake\\Utility\\String::tail)
* `truncate` maps to [`Cake\Utility\String::truncate`](http://book.cakephp.org/3.0/en/core-libraries/string.html#Cake\\Utility\\String::truncate)
* `excerpt` maps to [`Cake\Utility\String::excerpt`](http://book.cakephp.org/3.0/en/core-libraries/string.html#Cake\\Utility\\String::excerpt)
* `toList` maps to [`Cake\Utility\String::toList`](http://book.cakephp.org/3.0/en/core-libraries/string.html#Cake\\Utility\\String::toList)
* `stripLinks` maps to [`Cake\Utility\String::stripLinks`](http://book.cakephp.org/3.0/en/core-libraries/string.html#Cake\\Utility\\String::stripLinks)
* `isMultibyte` maps to [`Cake\Utility\String::isMultibyte`](http://book.cakephp.org/3.0/en/core-libraries/string.html#Cake\\Utility\\String::isMultibyte)
* `utf8` maps to [`Cake\Utility\String::utf8`](http://book.cakephp.org/3.0/en/core-libraries/string.html#Cake\\Utility\\String::utf8)
* `ascii` maps to [`Cake\Utility\String::ascii`](http://book.cakephp.org/3.0/en/core-libraries/string.html#Cake\\Utility\\String::ascii)
* `serialize` maps to [`serialize`](http://php.net/serialize)
* `unserialize` maps to [`unserialize`](http://php.net/unserialize)
* `md5` maps to [`md5`](http://php.net/md5)
* `base64_encode` maps to [`base64_encode`](http://php.net/base64_encode)
* `base64_decode` maps to [`base64_decode`](http://php.net/base64_decode)
* `nl2br` maps to [`nl2br`](http://php.net/nl2br)
* `string` cast to [`string`](http://php.net/manual/en/language.types.type-juggling.php)

### Functions
* `in_array` maps to [`in_array`](http://php.net/in_array)
* `explode` maps to [`explode`](http://php.net/explode)
* `array` cast to [`array`](http://php.net/manual/en/language.types.type-juggling.php)
* `array_push` maps to [`push`](http://php.net/push)
* `array_add` maps to [`add`](http://php.net/add)
* `array_prev` maps to [`prev`](http://php.net/prev)
* `array_next` maps to [`next`](http://php.net/next)
* `array_current` maps to [`current`](http://php.net/current)
* `array_each` maps to [`each`](http://php.net/each)
* `__` maps to [`__`](http://book.cakephp.org/3.0/en/core-libraries/internationalization-and-localization.html)
* `__d` maps to [`__d`](http://book.cakephp.org/3.0/en/core-libraries/internationalization-and-localization.html)
* `__n` maps to [`__n`](http://book.cakephp.org/3.0/en/core-libraries/internationalization-and-localization.html)
* `__x` maps to [`__x`](http://book.cakephp.org/3.0/en/core-libraries/internationalization-and-localization.html)
* `__dn` maps to [`__dn`](http://book.cakephp.org/3.0/en/core-libraries/internationalization-and-localization.html)
* `defaultCurrency` maps to [`Cake\I18n\Number::defaultCurrency`](http://book.cakephp.org/3.0/en/core-libraries/number.html#Cake\\I18n\\Number::defaultCurrency)
* `number_formatter` maps to [`Cake\I18n\Number::formatter`](http://book.cakephp.org/3.0/en/core-libraries/number.html#Cake\\I18n\\Number::formatter)
* `uuid` maps to [`Cake\Utility\String::uuid`](http://book.cakephp.org/3.0/en/core-libraries/string.html#Cake\\Utility\\String::uuid)
* `time` passed the first and optional second argument into [`new \Cake\I18n\Time()`](http://book.cakephp.org/3.0/en/core-libraries/time.html#creating-time-instances)
* `timezones` maps to `Cake\I18n\Time::listTimezones`
* `elementExists` maps to `Cake\View\View::elementExists`,
* `getVars` maps to `Cake\View\View::getVars`
* `get` maps to `Cake\View\View::get`

### Twig
Visite [Twig Documentaion](http://twig.sensiolabs.org/documentation) for more tips

## Events ##

This plugin emits several events.

### Loaders ###

The default loader can be replace by listening to the `WyriHaximus\TwigView\Event\LoaderEvent::EVENT`, for example with [twital](https://github.com/goetas/twital):

```php
<?php

use Cake\Event\EventListenerInterface;
use Goetas\Twital\TwitalLoader;
use WyriHaximus\TwigView\Event\ConstructEvent;
use WyriHaximus\TwigView\Event\LoaderEvent;

class LoaderListener implements EventListenerInterface
{
    public function implementedEvents()
    {
        return [
            LoaderEvent::EVENT => 'loader',
            ConstructEvent::EVENT => 'construct',
        ];
    }

    public function loader(LoaderEvent $event)
    {
        $event->result = new TwitalLoader($event->getLoader());
    }

    /**
     * We've also listening in on this event so we can add the needed extensions to check for to the view
     */
    public function construct(ConstructEvent $event)
    {
        $event->getTwigView()->unshiftExtension('.twital.html');
        $event->getTwigView()->unshiftExtension('.twital.xml');
        $event->getTwigView()->unshiftExtension('.twital.xhtml');
    }
}


```

### Extensions ###

Extensions can be added to the twig environment by listening to the `WyriHaximus\TwigView\Event\ConstructEvent::EVENT`, for example:

```php
<?php

use Cake\Event\EventListenerInterface;
use WyriHaximus\TwigView\Event\ConstructEvent;

class LoaderListener implements EventListenerInterface
{
    public function implementedEvents()
    {
        return [
            ConstructEvent::EVENT => 'construct',
        ];
    }

    public function construct(ConstructEvent $event)
    {
        $event->getTwig()->addExtension(new YourTwigExtension);
    }
}

```

## Bake

You can use Bake to generate your basic CRUD views using the `theme` option.
Let's say you have a `TasksController` for which you want to generate twig templates.
You can use the following command to generate your index, add, edit and view file formatted
using Twig :

```bash
bin/cake bake twig_template Tasks all -t WyriHaximus/TwigView
```

## Screenshots ##

### Profiler ###

![Profiler](/docs/images/profiler.png)

### Templates found ###

![Templates found](/docs/images/templates-found.png)

## License ##

Copyright 2015 [Cees-Jan Kiewiet](http://wyrihaximus.net/)

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.
