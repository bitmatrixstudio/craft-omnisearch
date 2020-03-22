<template>
  <div class="omnisearch">
    <div class="omnisearch__active-filters"
         v-if="activeFilters.length > 0">
      <active-filter
        v-for="(filter, index) in activeFilters"
        :field-name="getFieldName(filter.field)"
        :data-type="getFieldDataType(filter.field)"
        :operator="filter.operator"
        :value="getValueText(filter.field, filter.value)"
        :key="index"
        @remove-filter="removeFilter(index)"
      />
    </div>
    <add-filter-button
      :fields="fields"
      @add-filter="addFilter"
    />
  </div>
</template>

<script>
import AddFilterButton from './AddFilterButton.vue';
import ActiveFilter from './ActiveFilter.vue';

export default {
  name: 'OmniSearch',
  components: { ActiveFilter, AddFilterButton },
  props: {
    fields: {
      type: Array,
      required: true,
      default: () => ([]),
    },
    initialFilters: {
      type: Array,
      default: () => ([]),
    },
  },
  data() {
    return {
      activeFilters: [...this.initialFilters],
    };
  },
  computed: {
    fieldMap() {
      return this.fields.reduce((fieldMap, field) => {
        Object.assign(fieldMap, {
          [field.handle]: field,
        });

        return fieldMap;
      }, {});
    },
  },
  watch: {
    activeFilters: {
      deep: true,
      handler() {
        this.updateFilterCriteria();
      },
    },
  },
  methods: {
    getFieldName(handle) {
      return this.fieldMap[handle] != null ? this.fieldMap[handle].name : '';
    },
    getFieldDataType(handle) {
      return this.fieldMap[handle] != null ? this.fieldMap[handle].dataType : null;
    },
    getValueText(handle, value) {
      const field = this.fieldMap[handle];

      if (field == null) {
        return '';
      }

      let valueText = value;
      if (field.items != null) {
        const listOption = field.items.find((item) => item.value === value);
        if (listOption != null) {
          valueText = listOption.label;
        }
      }

      return valueText;
    },
    addFilter(filter) {
      this.activeFilters.push(filter);
    },
    removeFilter(index) {
      this.activeFilters.splice(index, 1);
    },
    updateFilterCriteria() {
      const elementIndex = window?.Craft?.elementIndex;
      const criteria = elementIndex?.settings?.criteria;

      if (criteria == null) {
        return;
      }

      criteria.omnisearchFilters = [...this.activeFilters];
      elementIndex.updateElements();
    },
  },
};
</script>

<style lang="scss">
  .omnisearch {
    position: relative;
    margin-top: -1em;
    margin-bottom: 1em;

    .btn:not(.small) {
      font-size: 1em;
    }

    .omnisearch__active-filters {
      display: inline-block;
    }

    .omnisearch__filter-panel {
      display: block;
      padding: 0 !important;
    }

    .omnisearch__field-list-search {
      padding: 0.5rem;
    }

    .omnisearch__field-list-search + .omnisearch__filter-panel-body {
      padding-top: 0;
    }

    .omnisearch__filter-panel-body,
    .omnisearch__filter-panel-footer {
      padding: 0.5rem;
    }

    .omnisearch__list-item {
      cursor: pointer;
      padding: 10px 14px;
      margin: 0 -0.5rem;

      &:hover {
        background-color: #f3f7fc;
      }
    }
  }
</style>
