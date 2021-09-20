<template>
  <div class="omnisearch">
    <template v-if="activeFilters.length > 0">
      <active-filter
        v-for="(filter, index) in activeFilters"
        :field="getField(filter.field)"
        :operator="filter.operator"
        :value="filter.value"
        :index="index"
        :key="index"
        @update-filter="updateFilter(filter, $event)"
        @remove-filter="removeFilter(index)"
      />
    </template>
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
    getField(handle) {
      return this.fieldMap[handle] != null ? this.fieldMap[handle] : null;
    },
    addFilter(filter) {
      this.activeFilters.push(filter);
    },
    updateFilter(filter, data) {
      Object.assign(filter, data);
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
    display: inline-flex;
    flex-wrap: wrap;

    .btn:not(.small) {
      font-size: 1em;
    }

    .omnisearch__filter {
      display: flex;
      margin-bottom: 0.5em;
    }

    .omnisearch__filter-text {
      display: block;
      max-width: 20rem;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .omnisearch__filter-panel {
      display: block;
      padding: 0 !important;
      min-width: 10rem;
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
