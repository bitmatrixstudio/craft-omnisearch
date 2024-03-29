<!--
  - @copyright Copyright (c) 2021 Bitmatrix Studio
  - @license https://craftcms.github.io/license/
  -->

<template>
  <div data-testid="list-options">
    <div class="flex-grow texticon search icon">
      <input class="text"
             type="text"
             v-model="keyword"
             :placeholder="translate('Search...')"
             ref="keywordInput"
             data-testid="list-options-filter-input"
      />
    </div>
    <div class="filter-list-options">
      <div
        v-for="item in filteredListItems"
        class="filter-list-option"
        :style="{
          '--indent-level': getIndentLevel(item)
        }"
        data-testid="list-option"
        :key="item.value">
        <label>
          <input
            :type="isMultiSelect ? 'checkbox' : 'radio'"
            :value="item.value"
            v-model="selection"
            @change="onSelectionChange"
          />
          {{ item.label }}</label>
      </div>
    </div>
  </div>
</template>

<script>
import cloneDeep from 'lodash/cloneDeep';
import FilterMethodMixin from './FilterMethodMixin';

export default {
  name: 'ListFilter',
  inject: ['translate'],
  mixins: [FilterMethodMixin],
  model: {
    prop: 'value',
    event: 'change',
  },
  props: {
    value: {
      type: [String, Array],
    },
  },
  data() {
    return {
      keyword: '',
      selection: this.value ? cloneDeep(this.value) : null,
    };
  },
  computed: {
    filteredListItems() {
      return this.items.filter(
        (item) => item.label.toLowerCase().includes(this.keyword.toLowerCase()),
      );
    },
    isMultiSelect() {
      return Boolean(this.filterMethod.multiple);
    },
  },
  created() {
    if (this.value == null) {
      this.selection = this.isMultiSelect ? [] : null;
    }
  },
  methods: {
    getIndentLevel(item) {
      return item.level ? item.level - 1 : 0;
    },
    onSelectionChange() {
      this.$emit('input', this.selection);
    },
  },
  mounted() {
    this.$refs.keywordInput.focus();
  },
};
</script>

<style lang="scss">
  .filter-list-options {
    max-height: 15rem;
    overflow-x: auto;
    margin: 0.5rem -0.5rem 0;
  }

  .filter-list-option {
    --indent-level: 0;

    label {
      padding: 0.25rem 0.5rem;
      display: block;
      cursor: pointer;

      padding-left: calc((var(--indent-level) * 1rem) + 0.5rem);

      &:hover {
        background-color: #f3f7fc;
      }
    }
  }
</style>
