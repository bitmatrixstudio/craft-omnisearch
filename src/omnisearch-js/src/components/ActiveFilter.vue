<template>
  <div class="omnisearch__filter" :data-testid="`active-filter-${index}`">
    <div
      ref="button"
      class="btn small"
      data-testid="filter-button"
      @click="editFilter"
    >
      <span
        class="omnisearch__filter-text"><strong>{{ field.name }}</strong> {{ operatorLabel }} <template
        v-if="requiresValue">{{ valueText }}</template>
      </span>
      <button
        type="button"
        class="omnisearch__remove-filter-btn"
        data-testid="remove-filter-button"
        @click.stop="removeFilter">
        &times;
      </button>
    </div>
    <div
      v-if="edited"
      class="menu omnisearch__filter-panel omnisearch__choose-fields"
      ref="filterPanel"
      data-testid="filter-panel"
    >
      <div
        v-if="showSelectFilterMethod"
        class="omnisearch__filter-panel-body"
        data-testid="filterMethodList"
      >
        <div class="omnisearch__list-item"
             v-for="filterMethod in filterMethods"
             :data-testid="`filter-method-${filterMethod.operator}`"
             :key="filterMethod.operator"
             @click="selectFilterMethod(filterMethod)"
        >
          {{ filterMethod.label }}
        </div>
      </div>
      <div v-else>
        <div class="omnisearch__filter-panel-body" data-testid="compare-value">
          <component
            :is="filterComponentName"
            v-model="compareValue"
            :items="field.items != null ? field.items : null"
            :filter-method="selectedFilterMethod"
            @apply="applyFilter"
          />
        </div>
        <div class="omnisearch__filter-panel-footer">
          <button
            class="btn fullwidth"
            :class="{ disabled: compareValue == null }"
            type="button"
            :disabled="compareValue == null"
            @click="applyFilter"
            data-testid="applyFilterBtn">
            Apply Filter
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import cloneDeep from 'lodash/cloneDeep';
import { createPopper } from '@popperjs/core';
import operators from '../operators';
import DATATYPES from '../datatypes';
import TextFilter from '../filters/TextFilter.vue';
import NumberFilter from '../filters/NumberFilter.vue';
import BooleanFilter from '../filters/BooleanFilter.vue';
import ListFilter from '../filters/ListFilter.vue';
import DateFilter from '../filters/DateFilter.vue';
import { formatValues } from '../formatters';

export default {
  name: 'ActiveFilter',
  components: {
    ListFilter,
    BooleanFilter,
    NumberFilter,
    TextFilter,
    DateFilter,
  },
  props: {
    index: {
      type: Number,
      required: true,
    },
    field: {
      type: Object,
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
  data() {
    return {
      edited: false,
      showSelectFilterMethod: false,
      compareValue: this.value,
    };
  },
  computed: {
    config() {
      return operators.find((item) => item.operator === this.operator);
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
      const { field, compareValue, operator } = this;

      if (field == null) {
        return '';
      }

      const values = !Array.isArray(compareValue) ? [compareValue] : compareValue;

      const valueText = formatValues(field, operator, values);

      return field.dataType === DATATYPES.TEXT ? `"${valueText}"` : valueText;
    },
    filterMethods() {
      return operators.filter(
        (operator) => operator.dataTypes.includes(this.field.dataType),
      );
    },
    filterComponentName() {
      return `${this.field.dataType}-filter`;
    },
    selectedFilterMethod() {
      return this.filterMethods.find((method) => method.operator === this.operator);
    },
    hasValue() {
      const { compareValue } = this;

      const isNonEmptyScalar = !Array.isArray(compareValue)
        && (compareValue !== null && compareValue !== '');
      const isNonEmptyArray = Array.isArray(compareValue) && compareValue.length > 0;

      return isNonEmptyScalar || isNonEmptyArray;
    },
  },
  watch: {
    value(newValue) {
      this.compareValue = cloneDeep(newValue);
    },
    edited(edited) {
      if (edited) {
        this.$nextTick(() => {
          this.popper = createPopper(this.$refs.button, this.$refs.filterPanel, {
            placement: 'bottom-start',
          });
        });
      } else if (!edited && this.popper) {
        this.popper.destroy();
      }
    },
  },
  methods: {
    editFilter() {
      this.edited = true;
    },
    stopEditingFilter() {
      this.edited = false;
      this.reset();
    },
    reset() {
      this.compareValue = this.value;
    },
    removeFilter() {
      this.$emit('remove-filter');
    },
    selectFilterMethod(filterMethod) {
      console.log('select filter method', filterMethod);
    },
    applyFilter() {
      const { requiresValue = true } = this.selectedFilterMethod;

      if (!requiresValue || this.hasValue) {
        this.$emit('update-filter', {
          field: this.field.handle,
          operator: this.selectedFilterMethod.operator,
          value: this.compareValue,
        });

        this.stopEditingFilter();
      }
    },
    handleClickOutside(e) {
      if (this.edited === false) {
        return;
      }

      if (this.$refs.filterPanel
        && !this.$refs.filterPanel.contains(e.target)
        && this.$refs.button
        && !this.$refs.button.contains(e.target)) {
        this.stopEditingFilter();
      }
    },
  },
  mounted() {
    document.addEventListener('click', this.handleClickOutside, false);
  },
  beforeDestroy() {
    if (this.popper) {
      this.popper.destroy();
    }

    document.removeEventListener('click', this.handleClickOutside);
  },
};
</script>

<style lang="scss">
.omnisearch__filter {
  margin-right: 0.5em;

  > .btn {
    display: flex;
    align-items: center;
  }
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
