import Vue from 'vue';
import OmniSearch from './components/OmniSearch.vue';

const originalLocation = new URL(window.location);
const originalParams = new URLSearchParams(originalLocation.search);

window.onload = function onLoad() {
  const $ = window.jQuery;
  const searchInput = document.querySelector('.search input');
  const contentContainer = document.querySelector('#content');

  if (searchInput != null && contentContainer !== null) {
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
        const elementIndex = window?.Craft?.elementIndex;

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
          const filters = Array.from(originalParams.entries()).map(([key, val]) => {
            const [, field, operator] = key.match(/(\w+)\[(\w+)]/);

            const value = ['in', 'not_in'].includes(operator) ? val.split(',') : val;

            return {
              field,
              operator,
              value,
            };
          });

          if (filters.length > 0) {
            this.initialFilters = filters;
          }
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
            const handle = $(window.Craft.elementIndex.$source[0]).data('handle');
            let uri = 'entries';
            if (handle) {
              uri += `/${handle}`;
            }

            if (activeFilters.length > 0) {
              const parts = activeFilters.map((filter) => {
                const values = ['in', 'not_in'].includes(filter.operator) ? filter.value.join(',') : filter.value;

                return `${filter.field}[${filter.operator}]=${values}`;
              });

              uri += `?${parts.join('&')}`;
            }

            window.history.replaceState({}, '', window.Craft.getUrl(uri));
          }
        },
      },
    }).$mount(omnisearchContainer);
  }
};
