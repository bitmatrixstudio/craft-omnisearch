/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

import Vue from 'vue';
import OmniSearch from './components/OmniSearch.vue';
import { createQueryParams, parseQueryParams } from './utils';

const originalLocation = new URL(window.location);

window.onload = function onLoad() {
  const $ = window.jQuery;
  const searchInput = document.querySelector('.search input');
  const contentContainer = document.querySelector('#content');

  if (searchInput != null && contentContainer !== null && window?.Craft?.elementIndex != null) {
    const omnisearchContainer = document.createElement('div');

    contentContainer.before(omnisearchContainer);

    const translateFn = (text, params) => window?.Craft.t('omnisearch', text, params);

    new Vue({
      render(createElement) {
        return createElement(OmniSearch, {
          ref: 'omnisearch',
          props: {
            fields: this.fields,
            initialFilters: this.initialFilters,
            translateFn,
            language: window?.Craft?.language,
          },
          on: {
            change: this.onFilterChange,
          },
        });
      },
      data: {
        fields: [],
        activeFilters: [],
        initialFilters: [],
      },
      mounted() {
        this.loadFields();
        this.parseParams();
        const { elementIndex } = window.Craft;

        if (elementIndex) {
          elementIndex.on('selectSource', () => {
            this.reset();
            this.loadFields();
          });
          elementIndex.on('updateElements', () => {
            const hasActiveFilters = this.activeFilters.length > 0;
            const $reorderDragHandles = $(contentContainer).find('.tableview .move.icon');

            $reorderDragHandles.toggle(!hasActiveFilters);
          });
        }
      },
      methods: {
        reset() {
          this.$refs.omnisearch.reset();
        },
        parseParams() {
          this.initialFilters = parseQueryParams(originalLocation);
        },
        loadFields() {
          const elementIndex = window?.Craft?.elementIndex;

          if (elementIndex == null) {
            return;
          }

          const elementType = elementIndex?.elementType;
          const source = elementIndex?.instanceState?.selectedSource;

          if (elementType == null || source == null) {
            return;
          }

          $.ajax({
            method: 'get',
            url: `/actions/omnisearch/fields?elementType=${elementType}&source=${source}`,
          }).done((data) => {
            this.fields = data;
          });
        },
        onFilterChange(activeFilters) {
          this.activeFilters = activeFilters;
          const elementIndex = window?.Craft?.elementIndex;
          const criteria = elementIndex?.settings?.criteria;
          const hasFilters = activeFilters.length > 0;

          if (criteria == null) {
            return;
          }

          criteria.omnisearchFilters = [...activeFilters];
          elementIndex.updateElements();

          if (typeof window.history !== 'undefined') {
            let uri = window.Craft.path;

            if (hasFilters) {
              uri += `?${createQueryParams(activeFilters)}`;
            }

            window.history.replaceState({}, '', window.Craft.getUrl(uri));
          }
        },
      },
    }).$mount(omnisearchContainer);
  }
};
