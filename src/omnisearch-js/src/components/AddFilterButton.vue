<template>
  <div class="omnisearch__add-filter">
    <button type="button"
            class="btn small icon omnisearch__add-filter-btn"
            @click="toggleMenu"
            ref="button"
    >
      <template v-if="selectedField != null">
        <strong>{{ buttonText }}</strong>{{ operatorText }}
      </template>
      <template v-else>{{ buttonText }}</template>
    </button>
    <div v-if="showFieldMenu"
         class="menu omnisearch__filter-panel omnisearch__choose-fields"
         ref="filterPanel"
         data-test="filterPanel"
    >
      <template v-if="selectedField == null">
        <!-- Step 1: choose field from list -->
        <div class="omnisearch__field-list-search">
          <div class="flex-grow texticon search icon">
            <input class="text"
                   type="text"
                   v-model="keyword"
                   placeholder="Search attributes..."
                   ref="searchInput"
                   data-test="fieldSearchInput"
            />
          </div>
        </div>
        <div class="omnisearch__filter-panel-body" data-test="fieldList">
          <div class="omnisearch__list-item"
               data-test="fieldListItem"
               v-for="(field, index) in fieldList"
               :key="index"
               @click="setSelectedField(field)">
            {{ field.name }}
          </div>
        </div>
      </template>
      <template v-else-if="selectedFilterMethod == null">
        <!-- Step 2: choose filter method -->
        <div class="omnisearch__filter-panel-body" data-test="filterMethodList">
          <div class="omnisearch__list-item"
               data-test="filterMethodListItem"
               v-for="filterMethod in filterMethods"
               :key="filterMethod.operator"
               @click="selectFilterMethod(filterMethod)"
          >
            {{ filterMethod.label }}
          </div>
        </div>
      </template>
      <template v-else>
        <!-- Step 3: choose value -->
        <div class="omnisearch__filter-panel-body" data-test="compareValue">
          <input
            ref="compareValueTextInput"
            data-test="compareValueTextInput"
            class="text"
            type="text"
            v-model="compareValue"
            @keydown.enter="applyFilter"
          />
        </div>
        <div class="omnisearch__filter-panel-footer">
          <button
            class="btn fullwidth"
            :class="{ disabled: compareValue == null }"
            type="button"
            :disabled="compareValue == null"
            @click="applyFilter"
            data-test="applyFilterBtn">
            Apply Filter
          </button>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import { createPopper } from '@popperjs/core';
import sortBy from 'lodash/sortBy';

export default {
  name: 'AddFilterButton',
  props: {
    fields: {
      type: Array,
      required: true,
      default: () => ([]),
    },
  },
  data() {
    return {
      keyword: '',
      selectedField: null,
      selectedFilterMethod: null,
      compareValue: null,
      showFieldMenu: false,
    };
  },
  computed: {
    buttonText() {
      if (this.showFieldMenu) {
        return this.selectedField ? this.selectedField.name : 'Choose Field';
      }

      return '+ Add Filter';
    },
    operatorText() {
      if (this.selectedFilterMethod != null) {
        return ` ${this.selectedFilterMethod.label}`;
      }

      return '';
    },
    fieldList() {
      const filteredFields = this.fields.filter(
        (field) => field.name.toLowerCase().includes(this.keyword.toLowerCase()),
      );

      return sortBy(filteredFields, 'name');
    },
    filterMethods() {
      return [
        { operator: 'contain', label: 'contains' },
        { operator: 'not_contain', label: 'does not contains' },
        { operator: 'equal', label: 'equals' },
        { operator: 'not_equal', label: 'not equal to' },
        { operator: 'starts_with', label: 'starts with' },
        { operator: 'is_present', label: 'is present', requiresValue: false },
        { operator: 'is_not_present', label: 'is not present', requiresValue: false },
      ];
    },
  },
  watch: {
    showFieldMenu(show) {
      if (show) {
        this.reset();

        this.$nextTick(() => {
          this.popper = createPopper(this.$refs.button, this.$refs.filterPanel, {
            placement: 'bottom-start',
          });

          this.$refs.searchInput.focus();
        });
      } else if (!show && this.popper) {
        this.popper.destroy();
      }
    },
  },
  methods: {
    reset() {
      this.selectedField = null;
      this.selectedFilterMethod = null;
      this.compareValue = null;
    },
    toggleMenu() {
      this.showFieldMenu = !this.showFieldMenu;
    },
    setSelectedField(field) {
      this.selectedField = field;
    },
    selectFilterMethod(method) {
      const { requiresValue = true } = method;

      this.selectedFilterMethod = method;

      if (!requiresValue) {
        this.applyFilter();
      } else {
        this.$nextTick(() => {
          if (this.$refs.compareValueTextInput != null) {
            this.$refs.compareValueTextInput.focus();
          }
        });
      }
    },
    applyFilter() {
      const { requiresValue = true } = this.selectedFilterMethod;

      if (!requiresValue || (this.compareValue !== null && this.compareValue !== '')) {
        this.$emit('add-filter', {
          field: this.selectedField.handle,
          operator: this.selectedFilterMethod.operator,
          value: this.compareValue,
        });

        this.showFieldMenu = false;
        this.reset();
      }
    },
  },
  beforeDestroy() {
    if (this.popper) {
      this.popper.destroy();
    }
  },
};
</script>

<style lang="scss">
  .omnisearch__add-filter {
    display: inline-block;
  }
</style>
