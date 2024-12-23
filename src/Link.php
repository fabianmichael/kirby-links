<?php

namespace FabianMichael\Links;

use Kirby\Cms\Block;
use Kirby\Cms\File;
use Kirby\Cms\ModelWithContent;
use Kirby\Cms\Page;
use Kirby\Cms\StructureObject;
use Kirby\Cms\Url;
use Kirby\Content\Content;
use Kirby\Content\Field;
use Kirby\Toolkit\Obj;
use Kirby\Uuid\Uuid;

class Link
{
	public static function resolve(
		Page|Block|StructureObject|string|null $link,
		array $options = []): ?Obj
	{
		if (is_null($link)) {
			// accepting `null` a input makes the function easier to use
			return null;
		}

		$result = [
			'href' => null,
			'current' => false,
			'external' => false,
			'target' => null,
			'download' => false,
		];

		if (is_string($link)) {
			// just a string, use directly as URL
			$result = array_merge($result, [
				'href' => $link,
			]);
		} elseif ($link instanceof Page) {
			// plain page object
			$result = array_merge($result, [
				'href' => $link->url(),
				'current' => $link === page(),
				'page' => $link,
				'text' => $link->title()->toString(),
			]);
		} elseif ($link instanceof File) {
			// plain file object
			$result = array_merge($result, [
				'href' => $link->url(),
				'file' => $link,
				'text' => $link->filename(),
			]);
		} elseif (is_a($link, Field::class)) {
			// link field
			$href = $link->toUrl();

			if ($href === null) {
				return null;
			}

			$result = array_merge($result, [
				'href' => (string) $href,
			]);
		} elseif (is_a($link, Block::class)
			|| is_a($link, StructureObject::class)
			|| is_a($link, Content::class)) {
			// Link field group from various sources

			$result['target'] = r($link->new_tab()->toBool(), '_blank');
			$value = $link->link()->value();
			$href = $link->toUrl();

			if ($link === null) {
				return null;
			}

			if (Uuid::is($value, 'page')) {
				if (!$model = Uuid::for($value)->model()) {
					return null;
				}
				
				/** @var Page $model */
				$result = array_merge($result, [
					'href' => $model->url(),
					'page' => $model,
					'current' => static::ariaCurrentValue($model),
					'text' => $link->text()->or($model->title())->toString(),
				]);
			} elseif (Uuid::is($value, 'file')) {
				if (!$model = Uuid::for($value)->model()) {
					return null;
				}
				
				/** @var Page $model */
				$result = array_merge($result, [
					'href' => $model->url(),
					'file' => $model,
					'text' => $link->text()->or($model->filename())->toString(),
				]);
			}
		}

		$result = array_merge($result, $options);

		if (! empty($result['href'])) {
			$rel = [];

			$external = parse_url($result['href'], PHP_URL_HOST) !== parse_url(kirby()->url('index'), PHP_URL_HOST);

			if ($external) {
				$rel[] = 'external';
			}

			if ($external && ! is_null($result['target'])) {
				$rel = [...$rel, 'noopener', 'noreferrer'];
			}

			$result = new Obj(array_merge($result, [
				'text' => new Field(null, 'text', $result['text'] ?? Url::short($result['href'])),
				'rel' => $rel = count($rel) > 0 ? implode(' ', $rel) : null,
				'external' => $external,
				'attr' => attributes([
					'href' => $result['href'],
					'rel' => $rel,
					'target' => $result['target'],
					'aria-current' => $result['current'],
					'download' => $result['download'] ? true : null,
				]),
			]));

			return $result;
		}

		return null;
	}

	public static function ariaCurrentValue(ModelWithContent $model): string|bool
	{
		if (!$model instanceof Page) {
			return false;
		}

		if ($model->isActive()) {
			return 'page';}

		if ($model->isOpen()) {
			return 'true';
		}

		return false;
	}
}
