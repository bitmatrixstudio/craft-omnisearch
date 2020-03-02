import Vue from 'vue';
import OmniSearch from './components/OmniSearch.vue';

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
          props: {
            fields: this.fields,
          },
        });
      },
      data: {
        fields: [],
      },
      mounted() {
        this.loadFields();
      },
      methods: {
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
      },
    }).$mount(omnisearchContainer);
  }
};
