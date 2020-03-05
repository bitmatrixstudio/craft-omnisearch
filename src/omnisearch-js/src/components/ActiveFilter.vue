<template>
  <div class="omnisearch__filter btn small" data-test="activeFilter">
    <span
      class="omnisearch__filter-text"><strong>{{ fieldName }}</strong> {{ operatorLabel }}</span>
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
      type: [String, Number, Array],
      default: null,
    },
  },
  computed: {
    operatorLabel() {
      const { operator, value, dataType } = this;

      const config = OPERATORS.find((item) => item.operator === operator);
      if (config == null) {
        return 'Invalid operator';
      }

      const { requiresValue = true } = config;

      let labelTemplate = '{operator}';

      if (requiresValue) {
        labelTemplate += dataType === DATATYPES.TEXT ? ' "{value}"' : ' {value}';
      }

      return labelTemplate
        .replace('{operator}', config.label)
        .replace('{value}', value);
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
