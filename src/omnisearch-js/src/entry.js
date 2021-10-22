/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

import Vue from 'vue';
import OmniSearch from './components/OmniSearch.vue';
import { createQueryParams, parseQueryParams, waitFor } from './utils';

const originalLocation = new URL(window.location);
const $ = window.jQuery;

const translateFn = (text, params) => window?.Craft.t('omnisearch', text, params);

function createOmniSearch({
  container,
  elementIndex,
  updateHistory = false,
}) {
  const omnisearchContainer = document.createElement('div');
  container.before(omnisearchContainer);

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
      elementIndex,
      fields: [],
      activeFilters: [],
      initialFilters: [],
    },
    mounted() {
      this.loadFields();
      this.parseParams();

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
        if (updateHistory) {
          this.initialFilters = parseQueryParams(originalLocation);
        }
      },
      loadFields() {
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
        const criteria = elementIndex?.settings?.criteria;
        const hasFilters = activeFilters.length > 0;

        if (criteria == null) {
          return;
        }

        criteria.omnisearchFilters = [...activeFilters];
        elementIndex.updateElements();

        if (typeof window.history !== 'undefined' && updateHistory) {
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

window.onload = () => {
  const searchInput = document.querySelector('.search input');
  const contentContainer = document.querySelector('#content');

  if (searchInput != null && contentContainer !== null && window?.Craft?.elementIndex != null) {
    createOmniSearch({
      container: contentContainer,
      elementIndex: window.Craft.elementIndex,
      updateHistory: true,
    });
  }

  // Hook onto element selector modals
  const $elementSelectors = $('.elementselect, .categoriesfield');

  $elementSelectors.each((index, el) => {
    const $el = $(el);

    // Attach event handler on first click
    $el.find('.btn.add').one('click', async () => {
      const data = $el.data('elementSelect');
      await waitFor(() => data.modal.elementIndex);

      const $container = data.modal.elementIndex.$elements.get(0);

      createOmniSearch({
        container: $container,
        elementIndex: data.modal.elementIndex,
      });
    });
  });
};
