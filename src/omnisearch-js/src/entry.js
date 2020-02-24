import Vue from 'vue';
import OmniSearch from './components/OmniSearch.vue';

window.onload = function () {
  const searchInput = document.querySelector('.search input');

  if (searchInput !== null) {
    const omnisearchContainer = document.createElement('div');

    searchInput.after(omnisearchContainer);

    new Vue({
      render: (h) => h(OmniSearch),
    }).$mount(omnisearchContainer);
  }
};
