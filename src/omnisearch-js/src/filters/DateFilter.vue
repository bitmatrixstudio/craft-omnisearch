<template>
  <date-picker
    :value="pickerValue"
    @input="onChange"
    :is-range="isRange"
  />
</template>

<script>
import DatePicker from 'v-calendar/lib/components/date-picker.umd';
import FilterMethodMixin from './FilterMethodMixin';
import { parseDateRange } from '../utils';

export default {
  name: 'DateFilter',
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

        const newValue = `${start.toISOString().substr(0, 10)},${end.toISOString().substr(0, 10)}`;
        this.$emit('input', newValue);
      } else {
        this.$emit('input', value.toISOString().substr(0, 10));
      }
    },
  },
};
</script>
