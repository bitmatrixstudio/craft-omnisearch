<template>
  <div class="omnisearch__add-filter">
    <button type="button"
            class="btn small icon omnisearch__add-filter-btn"
            @click="onAddFilterBtnClick"
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
      <div v-show="selectedField == null">
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
      </div>
      <div v-show="selectedField != null && selectedFilterMethod == null">
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
      </div>
      <div v-if="selectedField != null && selectedFilterMethod != null">
        <!-- Step 3: choose value -->
        <div class="omnisearch__filter-panel-body" data-test="compareValue">
          <component
            :is="selectedFieldDataType + '-filter'"
            v-model="compareValue"
            :items="selectedField.items"
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
            data-test="applyFilterBtn">
            Apply Filter
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { createPopper } from '@popperjs/core';
import sortBy from 'lodash/sortBy';
import operators from '../operators';
import DATATYPES from '../datatypes';
import TextFilter from '../filters/TextFilter.vue';
import NumberFilter from '../filters/NumberFilter.vue';
import BooleanFilter from '../filters/BooleanFilter.vue';
import ListFilter from '../filters/ListFilter.vue';

export default {
  name: 'AddFilterButton',
  components: {
    ListFilter,
    BooleanFilter,
    NumberFilter,
    TextFilter,
  },
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
      DATATYPES,
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
    selectedFieldDataType() {
      return this.selectedField != null ? this.selectedField.dataType : null;
    },
    filterMethods() {
      if (this.selectedField == null) {
        return [];
      }

      return operators.filter(
        (operator) => operator.dataTypes.includes(this.selectedField.dataType),
      );
    },
  },
  watch: {
    showFieldMenu(show) {
      this.reset();

      if (show) {
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
    onAddFilterBtnClick() {
      const { showFieldMenu } = this;

      if (!showFieldMenu) {
        this.showFieldMenu = true;
      } else if (this.selectedField != null) {
        this.selectedField = null;
      } else {
        this.closeMenu();
      }
    },
    reset() {
      this.selectedField = null;
      this.selectedFilterMethod = null;
      this.compareValue = null;
      this.keyword = '';
    },
    closeMenu() {
      this.showFieldMenu = false;
      this.reset();
    },
    setSelectedField(field) {
      this.selectedField = field;
    },
    selectFilterMethod(method) {
      const { requiresValue = true } = method;

      this.selectedFilterMethod = method;

      if (!requiresValue) {
        this.applyFilter();
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

        this.closeMenu();
      }
    },
    handleClickOutside(e) {
      if (this.showFieldMenu === false) {
        return;
      }

      if (this.$refs.filterPanel
        && !this.$refs.filterPanel.contains(e.target)
        && this.$refs.button
        && !this.$refs.button.contains(e.target)) {
        this.closeMenu();
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
  .omnisearch__add-filter {
    display: inline-block;
  }
</style>
