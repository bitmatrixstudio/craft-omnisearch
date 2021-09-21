<template>
  <div
    class="menu omnisearch__filter-panel omnisearch__choose-fields"
    data-testid="filter-panel"
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
                 data-testid="field-search-input"
          />
        </div>
      </div>
      <div class="omnisearch__filter-panel-body" data-testid="fieldList">
        <div class="omnisearch__list-item"
             v-for="(field, index) in fieldList"
             :data-testid="`field-list-item-${field.handle}`"
             :key="index"
             @click="selectField(field)">
          {{ field.name }}
        </div>
      </div>
    </div>
    <div v-show="selectedField != null && selectedFilterMethod == null">
      <!-- Step 2: choose filter method -->
      <div class="omnisearch__filter-panel-body" data-testid="filterMethodList">
        <div class="omnisearch__list-item"
             v-for="filterMethod in filterMethods"
             :data-testid="`filter-method-${filterMethod.operator}`"
             :key="filterMethod.operator"
             @click="selectFilterMethod(filterMethod)"
        >
          {{ filterMethod.label }}
        </div>
      </div>
    </div>
    <div v-if="selectedField != null && selectedFilterMethod != null">
      <!-- Step 3: choose value -->
      <div class="omnisearch__filter-panel-body" data-testid="compare-value">
        <div class="btn menubtn omnisearch__filter-method-dropdown"
             @click.stop="changeFilterMethod">
          {{ selectedFilterMethod.label }}
        </div>
        <component
          v-if="requiresValue"
          :is="filterComponentName"
          :value="compareValue"
          :items="selectedField.items != null ? selectedField.items : null"
          :filter-method="selectedFilterMethod"
          @input="onCompareValueChange"
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
          data-testid="apply-filter-btn">
          Apply Filter
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import sortBy from 'lodash/sortBy';
import ListFilter from '../filters/ListFilter.vue';
import BooleanFilter from '../filters/BooleanFilter.vue';
import NumberFilter from '../filters/NumberFilter.vue';
import TextFilter from '../filters/TextFilter.vue';
import DateFilter from '../filters/DateFilter.vue';
import operators from '../operators';

export default {
  name: 'FilterPanel',
  components: {
    ListFilter,
    BooleanFilter,
    NumberFilter,
    TextFilter,
    DateFilter,
  },
  props: {
    fields: {
      type: Array,
      required: true,
      default: () => ([]),
    },
    selectedField: {
      type: Object,
    },
    selectedFilterMethod: {
      type: Object,
    },
    compareValue: {
      type: [Object, String, Number, Array],
    },
  },
  data() {
    return {
      keyword: '',
    };
  },
  computed: {
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
    filterComponentName() {
      return `${this.selectedFieldDataType}-filter`;
    },
    requiresValue() {
      const { requiresValue = true } = this.selectedFilterMethod;

      return requiresValue;
    },
  },
  methods: {
    selectField(field) {
      this.$emit('update:selected-field', field);
    },
    selectFilterMethod(method) {
      const { requiresValue = true, multiple = false } = method;
      const isArray = Array.isArray(this.compareValue);

      this.$emit('update:selected-filter-method', method);

      if ((isArray && !multiple) || (!isArray && multiple) || !requiresValue) {
        this.$emit('update:compare-value', null);
      }

      if (!requiresValue) {
        this.applyFilter();
      }
    },
    onCompareValueChange(value) {
      this.$emit('update:compare-value', value);
    },
    changeFilterMethod() {
      this.$emit('update:selected-filter-method', null);
    },
    applyFilter() {
      this.$emit('apply');
    },
  },
  mounted() {
    this.$refs.searchInput.focus();
  },
};
</script>
