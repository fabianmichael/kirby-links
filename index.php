<?php

use Kirby\Cms\App as Kirby;

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('fabianmichael/links', [
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
	'blocksMethods' => [
		'hasValidLinks' => function (): bool
		{
			foreach ($this as $block) {
				if (resolve_link($block) !== null) {
					return true;
				}
			}

			return false;
		}
	]
]);
