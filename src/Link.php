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
use Kirby\Toolkit\Str;
use Kirby\Uuid\Uuid;
use Stringable;

class Link extends Obj implements Stringable
{
	/**
	 * Tries to resolve the link and returns an instance of this
	 * class if that was successful.
	 */
	public static function resolve(
		Page|Block|StructureObject|string|null $link,
		array $options = []
	): ?Obj {

		if ($link === null) {
			// accepting `null` as input makes the function easier to use
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
			// just a string, use directly as URL without further validation
			$result = array_merge($result, [
				'href' => $link,
			]);
		} elseif ($link instanceof Page) {
			// plain page object
			$result = array_merge($result, [
				'href' => $link->url(),
				'current' => static::ariaCurrentValue($link),
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
		} elseif ($link instanceof Field) {
			// link field
			$href = $link->toUrl();

			if (empty($href)) {
				return null;
			}

			$result = array_merge($result, [
				'href' => (string) $href,
			]);
		} elseif ($link instanceof Block
			|| $link instanceof StructureObject
			|| $link instanceof Content
		) {
			// Link field group from various sources

			$result['target'] = r($link->new_tab()->toBool(), '_blank');
			$value = $link->link()->value();
			$href = $link->link()->toUrl();

			if (empty($href)) {
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
			} else if ($href !== null) {
				$result = array_merge($result, [
					'href' => $href,
					'text' => $link->text()->value(),
				]);
			}
		}

		$result = array_merge($result, $options);

		$rel = [];

		$external = parse_url($result['href'], PHP_URL_HOST) !== parse_url(kirby()->url('index'), PHP_URL_HOST);

		if ($external) {
			$rel[] = 'external';
		}

		if ($external && ! is_null($result['target'])) {
			$rel = [...$rel, 'noopener', 'noreferrer'];
		}

		return new static(array_merge($result, [
			'text' => new Field(null, 'text', $result['text'] ?: static::fallbackText($result['href'])),
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
	}

	/**
	 * Returns the `aria-current` value as string for given page object
	 * or boolean `false` if the $model is not the current page.
	 */
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

	/**
	 * Generates a fallback text for given URL.
	 */
	protected static function fallbackText(string $href): string {
		if (parse_url($href, PHP_URL_SCHEME) === 'mailto') {
			// Url::short() does not support `mailto:` links
			return parse_url($href, PHP_URL_PATH);
		}
		
		return Url::short($href);
	}

	/**
	 * Converts the link object to HTML.
	 */
	public function __toString() : string {
		return Str::template('<a {attr}>{text}</a>', [
			'attr' => $this->attr()->toString(),
			'text' => html($this->text()),
		]);
	}
}
