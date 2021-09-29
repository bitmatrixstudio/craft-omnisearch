<!--
  - @copyright Copyright (c) 2021 Bitmatrix Studio
  - @license https://craftcms.github.io/license/
  -->

<template>
  <input
    data-testid="compare-value-input"
    class="text"
    type="text"
    :value="value"
    @input="$emit('input', $event.target.value)"
    @keydown.enter="apply"
  />
</template>

<script>
import FilterMethodMixin from './FilterMethodMixin';

export default {
  name: 'TextFilter',
  mixins: [FilterMethodMixin],
  props: {
    value: String,
  },
  mounted() {
    this.$nextTick(() => {
      this.$el.focus();
    });
  },
};
</script>
