(function() {
  "use strict";
  function normalizeComponent(scriptExports, render, staticRenderFns, functionalTemplate, injectStyles, scopeId, moduleIdentifier, shadowMode) {
    var options = typeof scriptExports === "function" ? scriptExports.options : scriptExports;
    if (render) {
      options.render = render;
      options.staticRenderFns = staticRenderFns;
      options._compiled = true;
    }
    if (functionalTemplate) {
      options.functional = true;
    }
    if (scopeId) {
      options._scopeId = "data-v-" + scopeId;
    }
    var hook;
    if (moduleIdentifier) {
      hook = function(context) {
        context = context || // cached call
        this.$vnode && this.$vnode.ssrContext || // stateful
        this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext;
        if (!context && typeof __VUE_SSR_CONTEXT__ !== "undefined") {
          context = __VUE_SSR_CONTEXT__;
        }
        if (injectStyles) {
          injectStyles.call(this, context);
        }
        if (context && context._registeredComponents) {
          context._registeredComponents.add(moduleIdentifier);
        }
      };
      options._ssrRegister = hook;
    } else if (injectStyles) {
      hook = shadowMode ? function() {
        injectStyles.call(
          this,
          (options.functional ? this.parent : this).$root.$options.shadowRoot
        );
      } : injectStyles;
    }
    if (hook) {
      if (options.functional) {
        options._injectStyles = hook;
        var originalRender = options.render;
        options.render = function renderWithStyleInjection(h, context) {
          hook.call(context);
          return originalRender(h, context);
        };
      } else {
        var existing = options.beforeCreate;
        options.beforeCreate = existing ? [].concat(existing, hook) : [hook];
      }
    }
    return {
      exports: scriptExports,
      options
    };
  }
  const _sfc_main$1 = {};
  var _sfc_render$1 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("div", { on: { "dblclick": _vm.open } }, [_c("fm-link-preview", { attrs: { "content": _vm.content } })], 1);
  };
  var _sfc_staticRenderFns$1 = [];
  _sfc_render$1._withStripped = true;
  var __component__$1 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$1,
    _sfc_render$1,
    _sfc_staticRenderFns$1,
    false,
    null,
    null,
    null,
    null
  );
  __component__$1.options.__file = "/Users/fabian/Sites/stonedonsteel.org/site/plugins/links/panel/js/components/blocks/Link.vue";
  const Link = __component__$1.exports;
  const LinkPreview_vue_vue_type_style_index_0_lang = "";
  const _sfc_main = {
    props: {
      type: String,
      content: Object
    },
    data() {
      return {
        model: null,
        types: null
      };
    },
    props: {
      content: Object,
      url: {
        type: Boolean,
        default: true
      }
    },
    computed: {
      tag() {
        var _a;
        return ((_a = this.detected) == null ? void 0 : _a.link) ? "a" : "div";
      },
      currentType() {
        return this.type ?? this.detected.type;
      },
      icon() {
        var _a, _b, _c;
        if ((_b = (_a = this.model) == null ? void 0 : _a.image) == null ? void 0 : _b.icon) {
          return this.model.image.icon;
        }
        if (this.types === null) {
          this.types = this.$helper.link.types();
        }
        return ((_c = this.types[this.currentType]) == null ? void 0 : _c.icon) || "question";
      },
      detected() {
        return this.$helper.link.detect(this.content.link);
      },
      download() {
        var _a;
        return ["custom", "file"].includes(this.currentType) && ((_a = this.content) == null ? void 0 : _a.download);
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
        return this.content.new_tab;
      },
      text() {
        var _a;
        return this.content.text || ((_a = this.model) == null ? void 0 : _a.label) || "—";
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
  };
  var _sfc_render = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("div", { staticClass: "fm-menu-link" }, [_c("k-icon", { staticClass: "fm-menu-link__icon", attrs: { "type": _vm.icon } }), _c("div", { staticClass: "fm-menu-link__text" }, [_vm._v(_vm._s(_vm.text))]), _vm.download ? _c("k-icon", { staticClass: "fm-link-preview__icon", attrs: { "type": "download", "title": "Force download" } }) : _vm._e(), _vm.new_tab ? _c("k-icon", { staticClass: "fm-link-preview__icon", attrs: { "type": "open", "title": "Open in new tab" } }) : _vm._e()], 1);
  };
  var _sfc_staticRenderFns = [];
  _sfc_render._withStripped = true;
  var __component__ = /* @__PURE__ */ normalizeComponent(
    _sfc_main,
    _sfc_render,
    _sfc_staticRenderFns,
    false,
    null,
    null,
    null,
    null
  );
  __component__.options.__file = "/Users/fabian/Sites/stonedonsteel.org/site/plugins/links/panel/js/components/misc/LinkPreview.vue";
  const LinkPreview = __component__.exports;
  const index = "";
  panel.plugin("fabianmichael/links", {
    blocks: {
      link: Link
    },
    components: {
      "fm-link-preview": LinkPreview
    },
    icons: {
      external: '<path d="M10 6v2H5v11h11v-5h2v6a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h6Zm11-3v8h-2V6.413l-7.793 7.794-1.414-1.414L17.585 5H13V3h8Z"/>'
    }
  });
})();
