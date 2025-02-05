/* global panel */

import Link from './js/components/blocks/Link.vue'
import Submenu from './js/components/blocks/Submenu.vue'

import LinkPreview from './js/components/misc/LinkPreview.vue'

import './index.css'

panel.plugin('fabianmichael/links', {
  blocks: {
    link: Link,
    submenu: Submenu
  },
  components: {
    'fm-link-preview': LinkPreview
  },
  icons: {
    external: '<path d="M10 6v2H5v11h11v-5h2v6a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h6Zm11-3v8h-2V6.413l-7.793 7.794-1.414-1.414L17.585 5H13V3h8Z"/>'
  }
})
