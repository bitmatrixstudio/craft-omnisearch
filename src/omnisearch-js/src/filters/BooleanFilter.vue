<template>
  <div class="select">
    <div v-for="option in options" :key="option.value">
      <label data-test="compareValueRadio">
        <input
          type="radio"
          :value="option.value"
          :checked="value === option.value"
          @change="$emit('change', option.value)"
        /> {{ option.label }}</label>
    </div>
  </div>
</template>

<script>
import FilterMethodMixin from './FilterMethodMixin';

export default {
  name: 'BooleanFilter',
  mixins: [FilterMethodMixin],
  model: {
    prop: 'value',
    event: 'change',
  },
  props: {
    value: [Number, Boolean],
  },
  data() {
    return {
      options: [{ value: true, label: 'True' }, { value: false, label: 'False' }],
    };
  },
};
</script>
