<?php

use Kirby\Cms\App as Kirby;
use Kirby\Cms\Block;
use Kirby\Toolkit\A;

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('fabianmichael/links', [
	'blueprints' => [
		'blocks/link' => __DIR__ . '/blueprints/blocks/link.yml',
		'blocks/menu-link' => __DIR__ . '/blueprints/blocks/menu-link.yml',
		'fields/link-group' => __DIR__ . '/blueprints/fields/link-group.yml',
		'fields/link' => __DIR__ . '/blueprints/fields/link.yml',
		'fields/links' => __DIR__ . '/blueprints/fields/links.yml',
		'fields/navigation' => __DIR__ . '/blueprints/fields/navigation.yml',
	],
	'blocksMethods' => [
		'hasValidLinks' => function (): bool
		{
			return A::some([...$this], fn(Block $block) => resolve_link($block));
		}
	]
]);
