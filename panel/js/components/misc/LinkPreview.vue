<template>
  <div class="fm-menu-link">
    <k-icon :type="icon" class="fm-menu-link__icon" />
    <div class="fm-menu-link__text">{{ text }}</div>
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
    // icon() {
    //   return this.link;
    // },
    // link() {
    //   const link_type = this.content.link_type
    //   if (link_type === "page" && this.content.target_page.length > 0) {
    //     let link = this.content.target_page[0]?.url
    //     if (this.content.anchor) link += "#" + this.content.anchor
    //     return link
    //   } else if (link_type === "url") {
    //     return this.content.url
    //   } else if (link_type === "file" && this.content.target_file.length > 0) {
    //     console.log("ff", this.content.target_file)
    //     return this.content.target_file[0]?.url
    //   }

    //   return null
    // },
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
.fm-menu-link {
  display: flex;
  font-size: var(--text-sm);
  font-weight: normal;
  line-height: 1.125rem;
  gap: var(--spacing-3);
}

.fm-menu-link__image {
  height: 100%;
  overflow: hidden;
	flex-shrink: 0;
}

.fm-menu-link__text,
.fm-menu-link__url {
  text-overflow: ellipsis;
  white-space: nowrap;
  overflow: hidden;
}

.fm-menu-link__text {
  overflow: hidden;
  text-overflow: ellipsis;
  min-width: 0;
  /* flex: 2 1 auto; */
}

.fm-menu-link__preview {
  flex: 0 2 auto;
  min-width: 0;
}

.fm-link-preview__icon {
  opacity: .5;
}

</style>
