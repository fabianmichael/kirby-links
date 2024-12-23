<?php

use FabianMichael\Links\Link;
use Kirby\Cms\Block;
use Kirby\Cms\Page;
use Kirby\Cms\StructureObject;
use Kirby\Toolkit\Obj;

function resolve_link(
	Page|Block|StructureObject|string|null $link,
	array $options = []): ?Obj
{
	return Link::resolve($link, $options);
}
