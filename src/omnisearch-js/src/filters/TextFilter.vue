<template>
  <input
    data-test="compareValueInput"
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
    this.$el.focus();
  },
};
</script>
