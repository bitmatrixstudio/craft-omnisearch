<!--
  - @copyright Copyright (c) 2021 Bitmatrix Studio
  - @license https://craftcms.github.io/license/
  -->

<template>
  <date-picker
    :locale="language"
    :value="pickerValue"
    @input="onChange"
    :is-range="isRange"
  />
</template>

<script>
import DatePicker from 'v-calendar/lib/components/date-picker.umd';
import dayjs from 'dayjs';
import FilterMethodMixin from './FilterMethodMixin';
import { parseDateRange } from '../utils';

export default {
  name: 'DateFilter',
  inject: ['language'],
  components: {
    DatePicker,
  },
  mixins: [FilterMethodMixin],
  props: {
    value: String,
  },
  computed: {
    isRange() {
      const { operator } = this.filterMethod;

      return operator === 'date_between';
    },
    pickerValue() {
      return this.isRange ? parseDateRange(this.value) : this.value;
    },
  },
  methods: {
    onChange(value) {
      if (this.isRange) {
        const { start, end } = value;
        const t1 = dayjs(start).startOf('day').format('YYYY-MM-DD');
        const t2 = dayjs(end).endOf('day').format('YYYY-MM-DD');

        const newValue = `${t1},${t2}`;
        this.$emit('input', newValue);
      } else {
        this.$emit('input', value.toISOString().substr(0, 10));
      }
    },
  },
};
</script>
