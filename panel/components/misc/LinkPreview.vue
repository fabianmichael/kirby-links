<template>
  <div class="fm-link-preview">
    <k-icon :type="icon" class="fm-link-preview__icon" />
    <div class="fm-link-preview__text">{{ text }}</div>
    <k-icon
      type="download"
      v-if="download"
      class="fm-link-preview__icon"
      title="Force download"
    />
    <k-icon
      type="open"
      v-if="new_tab"
      class="fm-link-preview__icon"
      title="Open in new tab"
    />
  </div>
</template>

<script>
export default {
  props: {
		type: String,
    content: Object,
	},
	data() {
		return {
			model: null,
      types: null,
		};
	},
  props: {
    content: Object,
    url: {
      type: Boolean,
      default: true,
    },
  },
  computed: {
    tag() {
      return this.detected?.link ? 'a' : 'div'
    },
    currentType() {
			return this.type ?? this.detected.type;
		},
    icon() {
      if (this.model?.image?.icon) {
        return this.model.image.icon;
      }

      if (this.types === null) {
        this.types = this.$helper.link.types();
      }

      return this.types[this.currentType]?.icon || "question"
    },
    detected() {
			return this.$helper.link.detect(this.content.link);
		},
    download() {
      return ['custom', 'file'].includes(this.currentType) && this.content?.download;
    },
    isLink() {
			return ["url", "email", "tel"].includes(this.currentType);
		},
    new_tab() {
      return this.content.new_tab
    },
    text() {
      return this.content.text || this.model?.label || "—"
    }
  },
  watch: {
		detected: {
			async handler(value, old) {
				if (value === old) {
					return;
				}

				this.model = await this.$helper.link.preview(this.detected);
			},
			immediate: true
		}
	}
}
</script>

<style>
.fm-link-preview {
  display: flex;
  font-weight: normal;
  line-height: 1.3;
  gap: .8em;
}

.fm-link-preview__image {
  height: 100%;
  overflow: hidden;
	flex-shrink: 0;
}

.fm-link-preview__text,
.fm-link-preview__url {
  text-overflow: ellipsis;
  white-space: nowrap;
  overflow: hidden;
}

.fm-link-preview__text {
  overflow: hidden;
  text-overflow: ellipsis;
  min-width: 0;
  /* flex: 2 1 auto; */
}

.fm-link-preview__preview {
  flex: 0 2 auto;
  min-width: 0;
}

.fm-link-preview__icon,
.fm-link-preview__icon {
  --icon-size: 1.25em;
}

.fm-link-preview__icon {
  opacity: .5;
}

</style>
