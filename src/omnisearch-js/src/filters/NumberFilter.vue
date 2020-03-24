<template>
  <input
    ref="compareValueInput"
    data-test="compareValueInput"
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
