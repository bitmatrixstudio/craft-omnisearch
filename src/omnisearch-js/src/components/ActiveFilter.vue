<template>
  <div class="omnisearch__filter btn small" data-testid="active-filter">
    <span
      class="omnisearch__filter-text"><strong>{{ fieldName }}</strong> {{ operatorLabel }} <template v-if="requiresValue">{{ valueText }}</template>
    </span>
    <button
      type="button"
      class="omnisearch__remove-filter-btn"
      @click="removeFilter">
      &times;
    </button>
  </div>
</template>
<script>
import OPERATORS from '../operators';
import DATATYPES from '../datatypes';

export default {
  name: 'FilterButton',
  props: {
    fieldName: {
      type: String,
      required: true,
    },
    dataType: {
      type: String,
      required: true,
    },
    operator: {
      type: String,
      required: true,
    },
    value: {
      type: [String, Number, Boolean, Array],
      default: null,
    },
  },
  computed: {
    config() {
      return OPERATORS.find((item) => item.operator === this.operator);
    },
    requiresValue() {
      const { config } = this;
      if (config == null) {
        return true;
      }

      return config.requiresValue != null ? config.requiresValue : true;
    },
    operatorLabel() {
      return this.config != null ? this.config.label : 'Invalid operator';
    },
    valueText() {
      return this.dataType === DATATYPES.TEXT ? `"${this.value}"` : this.value;
    },
  },
  methods: {
    removeFilter() {
      this.$emit('remove-filter');
    },
  },
};
</script>

<style lang="scss">
  .omnisearch__filter {
    margin-right: 0.5em;
  }

  .omnisearch__remove-filter-btn {
    border: none;
    border-radius: 50%;
    padding: 0;
    width: 16px;
    line-height: 1rem;
    margin-left: 0.25em;
    font-size: 16px;
    background-color: transparent;
    cursor: pointer;

    &:hover {
      color: #F00;
    }
  }
</style>
