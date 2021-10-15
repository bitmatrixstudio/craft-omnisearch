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

    new Vue({
      render(createElement) {
        return createElement(OmniSearch, {
          ref: 'omnisearch',
          props: {
            fields: this.fields,
            initialFilters: this.initialFilters,
          },
          on: {
            change: this.onFilterChange,
          },
        });
      },
      data: {
        fields: [],
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
          const elementIndex = window?.Craft?.elementIndex;
          const criteria = elementIndex?.settings?.criteria;

          if (criteria == null) {
            return;
          }

          criteria.omnisearchFilters = [...activeFilters];
          elementIndex.updateElements();

          if (typeof window.history !== 'undefined') {
            let uri = window.Craft.path;

            if (activeFilters.length > 0) {
              uri += `?${createQueryParams(activeFilters)}`;
            }

            window.history.replaceState({}, '', window.Craft.getUrl(uri));
          }
        },
      },
    }).$mount(omnisearchContainer);
  }
};
