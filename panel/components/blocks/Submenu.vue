<template>
  <div @dblclick="open" class="fm-menu-submenu">
    <div class="fm-menu-submenu__title">
      <k-icon type="bars" />
      <div>{{ title }}</div>
    </div>

    <ul>
      <li
        v-for="(item, id) in content.items"
        :key="id"
        class="fm-menu-submenu__item"
      >
        <fm-link-preview :content="item.content" />
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  computed: {
    title() {
      return this.content.title || "—"
    },
  },
}
</script>

<style>
.fm-menu-submenu__title {
  display: flex;
  gap: var(--spacing-4);
}

.fm-menu-submenu__item {
  padding-block-start: var(--spacing-4);
  padding-inline-start: var(--spacing-8);
}
</style>
