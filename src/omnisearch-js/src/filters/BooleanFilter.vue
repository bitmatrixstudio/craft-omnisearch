<!--
  - @copyright Copyright (c) 2021 Bitmatrix Studio
  - @license https://craftcms.github.io/license/
  -->

<template>
  <div class="select">
    <div v-for="option in options" :key="option.value">
      <label data-testid="compare-value-radio">
        <input
          type="radio"
          :value="option.value"
          :checked="value === option.value"
          @change="$emit('input', option.value)"
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
