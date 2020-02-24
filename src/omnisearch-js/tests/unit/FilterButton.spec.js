import { shallowMount } from '@vue/test-utils';
import Filter from '../../src/components/FilterButton.vue';

describe('FilterButton.vue', () => {
  let wrapper;

  beforeEach(() => {
    wrapper = shallowMount(Filter, {
      propsData: {
        filter: {
          field: {
            fieldName: 'Category',
          },
          operator: 'is_present',
        },
      },
    });
  });

  it('renders correctly', () => {
    expect(wrapper.classes()).toContain('omnisearch__filter');
    expect(wrapper.find('.omnisearch__filter-text').text()).toEqual('Category is present');
    expect(wrapper.find('.omnisearch__remove-filter-btn').exists()).toBe(true);
  });

  describe('filter methods', () => {
    it('renders correctly for "is_present" operator', () => {
      wrapper = shallowMount(Filter, {
        propsData: {
          filter: {
            field: {
              fieldName: 'Title',
            },
            operator: 'is_present',
          },
        },
      });

      expect(wrapper.find('.omnisearch__filter-text').text()).toEqual('Title is present');
    });

    it('renders correctly for "is_not_present" operator', () => {
      wrapper = shallowMount(Filter, {
        propsData: {
          filter: {
            field: { fieldName: 'Title' },
            operator: 'is_not_present',
          },
        },
      });

      expect(wrapper.find('.omnisearch__filter-text').text()).toEqual('Title is not present');
    });

    it('renders correctly for "starts_with" operator', () => {
      wrapper = shallowMount(Filter, {
        propsData: {
          filter: {
            field: { fieldName: 'Title' },
            operator: 'starts_with',
            value: 'ABC-123',
          },
        },
      });

      expect(wrapper.find('.omnisearch__filter-text').text()).
        toEqual('Title starts with "ABC-123"');
    });

    it('renders correctly for "contain" operator', () => {
      wrapper = shallowMount(Filter, {
        propsData: {
          filter: {
            field: { fieldName: 'Title' },
            operator: 'contain',
            value: 'Epic',
          },
        },
      });

      expect(wrapper.find('.omnisearch__filter-text').text()).toEqual('Title contains "Epic"');
    });

    it('renders correctly for "not_contain" operator', () => {
      wrapper = shallowMount(Filter, {
        propsData: {
          filter: {
            field: { fieldName: 'Title' },
            operator: 'not_contain',
            value: 'Epic',
          },
        },
      });

      expect(wrapper.find('.omnisearch__filter-text').text()).
        toEqual('Title does not contain "Epic"');
    });

    // equals
    // in
    // not_in

    // greater than
    // greater than or equal to

    // less than
    // less than or equal to
  });

  describe('remove filter button', () => {
    it('should emit "remove-filter" event when clicked', async () => {
      wrapper.find('.omnisearch__remove-filter-btn').trigger('click');

      expect(wrapper.emitted('remove-filter').length).toBe(1);
      expect(wrapper.emitted('remove-filter')[0]).toEqual([
        {
          field: { fieldName: 'Category' },
          operator: 'is_present',
        },
      ]);
    });
  });
});
