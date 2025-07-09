<?php

use FabianMichael\Links\Link;
use Kirby\Cms\App;
use Kirby\Cms\Block;
use Kirby\Cms\Blocks;
use Kirby\Cms\File;
use Kirby\Cms\Page;
use Kirby\Content\Field;
use Kirby\Filesystem\Asset;

@include_once __DIR__ . '/vendor/autoload.php';

App::plugin('fabianmichael/links', [
	'blueprints' => [
		'blocks/link' => __DIR__ . '/blueprints/blocks/link.yml',
		'blocks/menu-link' => __DIR__ . '/blueprints/blocks/menu-link.yml',
		'blocks/menu-submenu' => __DIR__ . '/blueprints/blocks/menu-submenu.yml',
		'fields/link-group' => __DIR__ . '/blueprints/fields/link-group.yml',
		'fields/link' => __DIR__ . '/blueprints/fields/link.yml',
		'fields/links' => __DIR__ . '/blueprints/fields/links.yml',
		'fields/navigation' => __DIR__ . '/blueprints/fields/navigation.yml',

		'links/blocks/link' => __DIR__ . '/blueprints/blocks/link.yml',
		'links/blocks/menu-link' => __DIR__ . '/blueprints/blocks/menu-link.yml',
		'links/blocks/menu-submenu' => __DIR__ . '/blueprints/blocks/menu-submenu.yml',
		'links/fields/link-group' => __DIR__ . '/blueprints/fields/link-group.yml',
		'links/fields/link' => __DIR__ . '/blueprints/fields/link.yml',
		'links/fields/links' => __DIR__ . '/blueprints/fields/links.yml',
		'links/fields/navigation' => __DIR__ . '/blueprints/fields/navigation.yml',
	],

	'assetMethods' => [
		'toResolvedLink' => function (): ?Link {
			/** @var Asset $this */
			return Link::resolve($this);
		},
	],
	'blockMethods' => [
		'toResolvedLink' => function (): ?Link {
			/** @var Block $this */
			return Link::resolve($this);
		},
	],
	'blocksMethods' => [
		'hasValidLinks' => function (): bool
		{
			/** @var Blocks $this */
			foreach ($this as $block) {
				if (Link::resolve($block) !== null) {
					return true;
				}
			}

			return false;
		},
	],
	'fieldMethods' => [
		'toResolvedLink' => function (): ?Link {
			/** @var Field $this */
			return Link::resolve($this);
		},
	],
	'fileMethods' => [
		'toResolvedLink' => function (): ?Link {
			/** @var File $this */
			return Link::resolve($this);
		},
	],
	'pageMethods' => [
		'toResolvedLink' => function (): ?Link {
			/** @var Page $this */
			return Link::resolve($this);
		},
	],
	'translations' => [
		'en' => require __DIR__ . '/translations/en.php',
		'de' => require __DIR__ . '/translations/de.php',
	],
]);
