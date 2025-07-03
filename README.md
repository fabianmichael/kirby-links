# Links plugin for Kirby CMS

This plugin improves link handling within Kirby by providing an extensive toolkit for managing links in the panel and using them in your templates.

**What’s in the box?**

- The all-mighty `resolve_link()` helper function ingests almost anything and tries to turn it into a link.
- Panel blueprints for editing single links, link lists and a simple menu builder (using Blocks fields under the hood).
- Nice block previews for links in the panel
- The blueprints are extensible and can eeasily be used for fancier stuff such as a button list or link with icons

## Installation

This version of the plugin requires PHP 8.4 and Kirby 4 or 5. Installations is currently only supported with composer:

$ composer require fabianmichael/kirby-links

## How it works & what is does

Dealing with links is a very commmon task for CMS and Kirby is no exception. Kirby comes with a built-in link field, but that does not support custom link texts, opening in new tabs, the download attribute etc.

This plugin uses the blocks field for handling single or multiple links, navigation lists and submenus. Each block has a set of fields for settings the details of each link. This approach still allows us to encapusle all link data in a single object, while making the plugin extensible, e.g. you can extend the blueprints for handling the style or tracking parameters of links.

## Getting started

### Single link

This example includes a link to the article author:

```yaml
# site/blueprints/pages/article.yml

title: Article

fields:
  […]
  author:
    extends: fields/link
    title: Author link
```

```php
# site/templates/article.php

<?php if ($link = resolve_link($page->author()->toBlocks()->first())): ?>
  <p>Author: <?= $link ?></p>
<?php endif ?>
```

## List of links


```yaml
# site/blueprints/pages/article.yml

title: Article

fields:
  […]
  authors:
    extends: fields/links
    title: Author links
```

```php
# site/templates/article.php

<?php $links = $page->authors()->toBlocks() ?>
<?php if ($links->hasValidLinks()): ?>
  <ul>
    <?php foreach ($links as $link): ?>
      <?php if ($link = resolve_link($link)): ?>
        <li><?= $link ?></li>
      <?php endif ?>
    <?php endforeach ?>
  </ul>
<?php endif ?>
```


### Navigation with submenus

… alrady implemented, docs coming soon


## Advanced usage

### Extending the blueprints

There are occasions, when you need more fields for your links, that what is included by default. A common example would be a list of buttons, where you can chose between primary and secondary button styles. The blueprints can easily be extended with additional fields.

```yaml
# site/blueprints/blocks/buttons.yml

name: Buttons
icon: bolt
fields:
  buttons:
    extends: fields/links
    fieldsets:
      button:
        extends: blocks/link
        fields:
          style:
            type: toggles
            label: Style
            default: primary
            options:
              primary: Primary
              secondary: Secondary
            required: true
```

```php
# site/snippets/blocks/buttons.php

<?php

$buttons = $block->buttons()->toBlocks();

if (!$buttons->hasValidLinks()) {
  return;
}

<ul>
  <?php foreach ($buttons as $button): ?>
    <?php if ($button = resolve_link($button)): ?>
      <?php $style = $button->style()->or('primary')->toString() ?>
      <li>
        <a <?= $button->attr()->merge([
          'class' => [
            'button',
            "button--{$style}"
          ],
        ]) ?>><?= html($button->text()) ?></a>
      </li>
    <?php endif ?>
  <?php endforeach ?>
</ul>
```

### Extending blueprints globally

All blueprints of the plugin are registred both directly and with a `links/` prefix, so you can override their options globally while still extending the original blueprints. The following example disables the new_tab field globally:

```
# site/blueprints/fields/link-group.yml

extends: links/fields/link-group

fields:
  new_tab: false

```
