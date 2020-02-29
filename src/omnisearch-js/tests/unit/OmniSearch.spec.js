import { shallowMount } from '@vue/test-utils';
import OmniSearch from '../../src/components/OmniSearch.vue';
import AddFilterButton from '../../src/components/AddFilterButton.vue';
import FilterButton from '../../src/components/FilterButton.vue';

describe('OmniSearch.vue', () => {
  let wrapper;
  let mockUpdateElementsFn;

  beforeEach(() => {
    mockUpdateElementsFn = jest.fn();

    window.Craft = {
      elementIndex: {
        settings: {
          context: 'index',
          storageKey: 'elementindex.craft\\elements\\Entry',
          criteria: {
            enabledForSite: null,
          },
        },
        updateElements: mockUpdateElementsFn,
      },
    };
  });

  describe('without initial filters', () => {
    beforeEach(() => {
      wrapper = shallowMount(OmniSearch, {
        propsData: {
          fields: [
            {
              fieldName: 'Title',
            },
            {
              fieldName: 'Post Date',
            },
          ],
        },
      });
    });

    it('renders correctly with no active filters', () => {
      expect(wrapper.element).toMatchSnapshot();
      expect(wrapper.classes()).toContain('omnisearch');
      expect(wrapper.find('.omnisearch__active-filters').exists()).toBe(false);
      expect(wrapper.findAll(AddFilterButton).length).toBe(1);
    });
  });

  describe('with initial filters', () => {
    beforeEach(() => {
      wrapper = shallowMount(OmniSearch, {
        propsData: {
          fields: [
            {
              fieldName: 'Title',
            },
            {
              fieldName: 'Post Date',
            },
          ],
          initialFilters: [
            {
              field: { fieldName: 'Title' },
              operator: 'is_present',
            },
          ],
        },
      });
    });

    it('renders correctly with initial active filters', () => {
      expect(wrapper.element).toMatchSnapshot();
      expect(wrapper.findAll('.omnisearch__active-filters').length).toEqual(1);
    });

    it('should add to activeFilters array and performSearch when addFilter event is emitted',
      async () => {
        wrapper.find(AddFilterButton).vm.$emit('add-filter', {
          field: { fieldName: 'Title' },
          operator: 'equals',
        });

        expect(wrapper.vm.activeFilters.length).toBe(2);
        await wrapper.vm.$nextTick();
        expect(window.Craft.elementIndex.settings.criteria.omnisearchFilters.length).toBe(2);
        expect(mockUpdateElementsFn).toHaveBeenCalledTimes(1);
      });

    it('should remove from activeFilters when removeFilter event is emitted', async () => {
      wrapper.find(FilterButton).vm.$emit('remove-filter', 0);

      expect(wrapper.vm.activeFilters.length).toBe(0);
      await wrapper.vm.$nextTick();
      expect(window.Craft.elementIndex.settings.criteria.omnisearchFilters.length).toBe(0);
      expect(mockUpdateElementsFn).toHaveBeenCalledTimes(1);
    });
  });

  // it('should perform a search when activeFilters change', () => {
  //
  // });
});
