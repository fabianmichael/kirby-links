<?php

use FabianMichael\Links\Link;
use Kirby\Cms\App;
use Kirby\Cms\Block;
use Kirby\Cms\Collection;
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
		'toResolvedLink' => function (array $overrides = []): ?Link {
			/** @var Asset $this */
			return Link::resolve($this, $overrides);
		},
	],
	'blockMethods' => [
		'toResolvedLink' => function (array $overrides = []): ?Link {
			/** @var Block $this */
			return Link::resolve($this, $overrides);
		},
	],
	'fieldMethods' => [
		'toResolvedLink' => function (Field $field, array $overrides = []): ?Link {
			return $field->toResolvedLinks($overrides)->first();
		},
		'toResolvedLinks' => function(Field $field, array $overrides = []): Collection {
			$blocks = $field->toBlocks();
			$links = new Collection();

			foreach ($blocks as $key => $block) {
				$link = $block->toResolvedLink($overrides);

				if ($link) {
					$links->append($key, $link);
				}
			}

			return $links;
		}
	],
	'fileMethods' => [
		'toResolvedLink' => function (array $overrides = []): ?Link {
			/** @var File $this */
			return Link::resolve($this, $overrides);
		},
	],
	'pageMethods' => [
		'toResolvedLink' => function (array $overrides = []): ?Link {
			/** @var Page $this */
			return Link::resolve($this, $overrides);
		},
	],
	'translations' => [
		'en' => require __DIR__ . '/translations/en.php',
		'de' => require __DIR__ . '/translations/de.php',
	],
]);
