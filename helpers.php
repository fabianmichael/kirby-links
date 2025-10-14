<?php

use FabianMichael\Links\Link;
use Kirby\Cms\Block;
use Kirby\Cms\Blocks;
use Kirby\Cms\File;
use Kirby\Cms\Page;
use Kirby\Cms\StructureObject;
use Kirby\Content\Content;
use Kirby\Filesystem\Asset;
use Kirby\Panel\Field;
use Kirby\Toolkit\Obj;

function resolve_link(
	Link|Page|File|Asset|Block|Blocks|Field|StructureObject|Content|string|null $link,
	array $options = []): ?Obj
{
	return Link::resolve($link, $options);
}
