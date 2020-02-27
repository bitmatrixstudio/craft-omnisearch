import Vue from 'vue';
import OmniSearch from './components/OmniSearch.vue';

window.onload = function onLoad() {
  const searchInput = document.querySelector('.search input');
  const omnisearchFilters = window.omnisearchFilters || {};

  if (searchInput !== null) {
    const omnisearchContainer = document.createElement('div');

    searchInput.after(omnisearchContainer);

    new Vue({
      render: (h) => h(OmniSearch, {
        props: {
          fields: omnisearchFilters,
        },
      }),
    }).$mount(omnisearchContainer);
  }
};
