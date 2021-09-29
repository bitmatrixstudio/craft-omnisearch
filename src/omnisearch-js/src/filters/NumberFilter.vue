<!--
  - @copyright Copyright (c) 2021 Bitmatrix Studio
  - @license https://craftcms.github.io/license/
  -->

<template>
  <input
    ref="compareValueInput"
    data-testid="compare-value-input"
    class="text"
    type="number"
    :value="value"
    @input="$emit('input', Number($event.target.value))"
    @keydown.enter="apply"
  />
</template>

<script>
import FilterMethodMixin from './FilterMethodMixin';

export default {
  name: 'NumberFilter',
  mixins: [FilterMethodMixin],
  props: {
    value: Number,
  },
  mounted() {
    this.$el.focus();
  },
};
</script>
