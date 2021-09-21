<template>
  <div class="omnisearch__filter" :data-testid="testId">
    <div
      ref="button"
      data-testid="filter-button"
      class="btn small icon"
      @click="onFilterButtonClick"
    >
      <span class="omnisearch__filter-text">
        <template v-if="selectedField != null"><strong>{{
            selectedField.name
          }}</strong>{{ operatorText }} {{ valueText }}</template>
        <template v-else-if="showFilterPanel">Choose Field</template>
        <template v-else>+ Add Filter</template>
      </span>
      <button
        v-if="!isNewFilter"
        type="button"
        class="omnisearch__remove-filter-btn"
        data-testid="remove-filter-button"
        @click.stop="removeFilter">
        &times;
      </button>
    </div>
    <filter-panel
      v-if="showFilterPanel"
      :fields="fields"
      :selected-field.sync="selectedField"
      :selected-filter-method.sync="selectedFilterMethod"
      :compare-value.sync="compareValue"
      ref="filterPanel"
      @apply="applyFilter"
    />
  </div>
</template>

<script>
import { createPopper } from '@popperjs/core';
import operators from '../operators';
import { formatValues } from '../formatters';
import FilterPanel from './FilterPanel.vue';

export default {
  name: 'FilterButton',
  components: {
    FilterPanel,
  },
  props: {
    fields: {
      type: Array,
      required: true,
      default: () => ([]),
    },
    filter: {
      type: Object,
    },
    testId: {
      type: String,
    },
  },
  data() {
    return {
      showFilterPanel: false,
      selectedField: null,
      selectedFilterMethod: null,
      compareValue: null,
    };
  },
  computed: {
    isNewFilter() {
      return this.filter == null;
    },
    operatorText() {
      return this.selectedFilterMethod != null ? ` ${this.selectedFilterMethod.label}` : '';
    },
    valueText() {
      const { selectedField, compareValue, selectedFilterMethod } = this;

      if (selectedField == null || selectedFilterMethod == null || !this.hasValue) {
        return '';
      }

      return formatValues(selectedField, selectedFilterMethod.operator, compareValue);
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
    showFilterPanel(show) {
      if (show) {
        this.$nextTick(() => {
          this.popper = createPopper(this.$refs.button, this.$refs.filterPanel.$el, {
            placement: 'bottom-start',
          });
        });
      } else if (!show && this.popper) {
        this.popper.destroy();
      }
    },
  },
  methods: {
    onFilterButtonClick() {
      const { showFilterPanel } = this;

      if (!showFilterPanel) {
        this.showFilterPanel = true;
      } else {
        this.closePanel(true);
      }
    },
    reset() {
      const { filter, isNewFilter } = this;

      if (isNewFilter) {
        this.selectedField = null;
        this.selectedFilterMethod = null;
        this.compareValue = null;
      } else {
        this.selectedField = this.fields.find((field) => field.handle === filter.field);
        this.selectedFilterMethod = operators.find(
          (operator) => operator.operator === filter.operator,
        );
        this.compareValue = filter.value;
      }
    },
    applyFilter() {
      const { requiresValue = true } = this.selectedFilterMethod;

      if (!requiresValue || this.hasValue) {
        this.$emit('apply', {
          field: this.selectedField.handle,
          operator: this.selectedFilterMethod.operator,
          value: this.compareValue,
        });
      }

      this.closePanel(this.isNewFilter);
    },
    removeFilter() {
      this.$emit('remove');
    },
    closePanel(reset = false) {
      this.showFilterPanel = false;

      if (reset) {
        this.reset();
      }
    },
    handleClickOutside(e) {
      if (this.showFilterPanel === false) {
        return;
      }

      if (this.$refs.filterPanel
        && !this.$refs.filterPanel.$el.contains(e.target)
        && this.$refs.button
        && !this.$refs.button.contains(e.target)) {
        this.closePanel(true);
      }
    },
  },
  created() {
    this.reset();
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
.omnisearch__add-filter {
  display: inline-block;
}

.omnisearch__filter-panel-body > input {
  width: 100%;
}

.omnisearch__filter-method-dropdown {
  display: flex;
  justify-content: space-between;
  width: 100%;
  margin-bottom: 0.5rem;

  &:after {
    display: block;
    content: '.';
    font-size: 0;
    width: 5px;
    height: 5px;
    border: solid #596673;
    border-width: 0 2px 2px 0;
    -webkit-transform: rotate(
        45deg);
    -o-transform: rotate(45deg);
    transform: rotate(45deg);
    position: relative;
  }
}

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
