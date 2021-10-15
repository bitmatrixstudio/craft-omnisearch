<!--
  - @copyright Copyright (c) 2021 Bitmatrix Studio
  - @license https://craftcms.github.io/license/
  -->

<template>
  <div class="omnisearch">
    <template v-if="fields.length > 0 && activeFilters.length > 0">
      <filter-button
        v-for="(filter, index) in activeFilters"
        :fields="fields"
        :filter="filter"
        :test-id="`active-filter-${index}`"
        :key="`filter-${index}`"
        @apply="updateFilter(filter, $event)"
        @remove="removeFilter(index)"
      />
    </template>
    <filter-button
      test-id="add-filter"
      :fields="fields"
      key="add-filter"
      @apply="addFilter"
    />
  </div>
</template>

<script>
import FilterButton from './FilterButton.vue';
import DATATYPES from '../datatypes';

export default {
  name: 'OmniSearch',
  components: { FilterButton },
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
      activeFilters: this.processFilters(this.initialFilters),
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
    initialFilters(newValue) {
      this.activeFilters = this.processFilters(newValue);
    },
    activeFilters: {
      deep: true,
      handler() {
        this.updateFilterCriteria();
      },
    },
  },
  methods: {
    reset() {
      this.activeFilters = [];
    },
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
      this.$emit('change', this.activeFilters);
    },
    processFilters(filters) {
      const { fields } = this;

      return filters.map((filter) => {
        const field = fields.find((item) => item.handle === filter.field);
        if (!field) {
          return filter;
        }

        return {
          ...filter,
          value: this.processValue(field.dataType, filter.value),
        };
      });
    },
    processValue(dataType, value) {
      switch (dataType) {
        case DATATYPES.BOOLEAN: {
          return value === 1 || value === '1' || value === 'true';
        }

        case DATATYPES.NUMBER: {
          return Number(value);
        }

        default: {
          return value;
        }
      }
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

  .omnisearch__filter-panel-body {
    max-height: 21rem;
    overflow-y: auto;
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

    &.omnisearch__list-item-group {
      cursor: default;
      font-weight: bold;

      &:hover {
        background-color: transparent;
      }
    }
  }

  .omnisearch__list-item-group-list {
    .omnisearch__list-item {
      padding-left: 24px;
    }
  }
}
</style>
