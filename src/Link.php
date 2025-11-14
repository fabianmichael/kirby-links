<?php

namespace FabianMichael\Links;

use FabianMichael\TemplateAttributes\Attributes;
use Kirby\Cms\Block;
use Kirby\Cms\Blocks;
use Kirby\Cms\File;
use Kirby\Cms\ModelWithContent;
use Kirby\Cms\Page;
use Kirby\Cms\StructureObject;
use Kirby\Cms\Url;
use Kirby\Content\Content;
use Kirby\Content\Field;
use Kirby\Filesystem\Asset;
use Kirby\Toolkit\Obj;
use Kirby\Toolkit\Str;
use Kirby\Uuid\Uuid;
use Stringable;

/**
 * @method Field text()
 * @method string href()
 * @method bool|string current()
 * @method bool external()
 * @method string|null target()
 * @method bool download()
 * @method string|null ariaLabel()
 * @method Content fields()
 * @method Attributes attr()
 */
class Link extends Obj implements Stringable
{
	private Content $contentData;

	/**
	 * Tries to resolve the link and returns an instance of this
	 * class if that was successful.
	 */
	public static function resolve(
		Link|Page|File|Asset|Block|Blocks|Field|StructureObject|Content|string|null $link,
		array $overrides = []
	): ?Obj {

		if ($link instanceof static) {
			// don’t do anything if we already have a resolved link
			return $link;
		}

		if ($link === null) {
			// accepting `null` as input makes the function easier to use
			return null;
		}

		// start with an empty link
		$result = [
			'href' => null,
			'current' => false,
			'external' => false,
			'target' => null,
			'download' => false,
			'text' => null,
			'ariaLabel' => null,
			'content' => [],
			'attr' => [],
		];

		if (is_string($link)) {
			// just a string, use directly as URL without further validation
			$result = array_merge($result, [
				'href' => $link,
			]);
		} elseif ($link instanceof Blocks) {
			return static::resolve($link->first(), $overrides);
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
			$result['ariaLabel'] = $link->aria_label()->value();
			$value = $link->link()->value();
			$href = $link->link()->toUrl();

			if (empty($href)) {
				return null;
			}

			$result['content'] = $link->content()->toArray();

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

		// apply overrides
		$result = array_merge($result, $overrides);

		// return `null` if href is empty
		if (empty($result['href'])) {
			return null;
		}

		// compute rel attribute
		$rel = static::relAttribute($result['href'], is_null($result['target']));
		$text = !empty($overrides['text'])
			? new Field(null, 'text', $overrides['text'])
			: new Field(null, 'text', !empty($result['text']) ? $result['text'] : static::fallbackText($result['href']));

		return new static(array_merge($result, [
			'text' => $text,
			'rel' => $rel,
			'external' => static::isExternal($result['href']),
			'attr' => attributes([
				'href' => $result['href'],
				'rel' => $rel,
				'target' => $result['target'],
				'aria-current' => $result['current'],
				'aria-label' => $result['ariaLabel'],
				'download' => $result['download'] ? true : null,
			])->merge($result['attr']),
		]));
	}

	public function fields(): Content
	{
		if (!isset($this->contentData)) {
			$this->contentData = new Content($this->content());
		}

		return $this->contentData;
	}

	public static function relAttribute(string $href, bool $newTab = false): ?string
	{
		$isExternal = parse_url($href, PHP_URL_HOST) !== parse_url(kirby()->url('index'), PHP_URL_HOST);
		$rel = [];

		if ($isExternal) {
			$rel[] = 'external';

			if ($newTab) {
				$rel = [...$rel, 'noopener', 'noreferrer'];
			}
		}

		return count($rel) > 0 ? implode(' ', $rel) : null;
	}


	public static function isExternal(string $href): bool
	{
		return parse_url($href, PHP_URL_HOST) !== parse_url(kirby()->url('index'), PHP_URL_HOST);
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
		$scheme = parse_url($href, PHP_URL_SCHEME);

		if (in_array($scheme, ['mailto', 'tel'])) {
			// Url::short() does not support `mailto:` or `tel:` links
			return parse_url($href, PHP_URL_PATH);
		} else if (in_array($scheme, ['http', 'https', 'ftp'])) {
			return Url::short($href);
		}

		return $href;
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
