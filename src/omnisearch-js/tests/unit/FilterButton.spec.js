/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

import { shallowMount } from '@vue/test-utils';
import FilterButton from '../../src/components/FilterButton.vue';
import DATATYPES from '../../src/datatypes';

describe('FilterButton.vue', () => {
  let wrapper;

  beforeEach(() => {
    wrapper = shallowMount(FilterButton, {
      propsData: {
        fieldName: 'Category',
        dataType: DATATYPES.TEXT,
        operator: 'is_present',
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
      wrapper = shallowMount(FilterButton, {
        propsData: {
          fieldName: 'Title',
          dataType: DATATYPES.TEXT,
          operator: 'is_present',
        },
      });

      expect(wrapper.find('.omnisearch__filter-text').text()).toEqual('Title is present');
    });

    it('renders correctly for "is_not_present" operator', () => {
      wrapper = shallowMount(FilterButton, {
        propsData: {
          fieldName: 'Title',
          dataType: DATATYPES.TEXT,
          operator: 'is_not_present',
        },
      });

      expect(wrapper.find('.omnisearch__filter-text').text()).toEqual('Title is not present');
    });

    it('renders correctly for "starts_with" operator', () => {
      wrapper = shallowMount(FilterButton, {
        propsData: {
          fieldName: 'Title',
          dataType: DATATYPES.TEXT,
          operator: 'starts_with',
          value: 'ABC-123',
        },
      });

      expect(wrapper.find('.omnisearch__filter-text').text())
        .toEqual('Title starts with "ABC-123"');
    });

    it('renders correctly for "contain" operator', () => {
      wrapper = shallowMount(FilterButton, {
        propsData: {
          fieldName: 'Title',
          dataType: DATATYPES.TEXT,
          operator: 'contain',
          value: 'Epic',
        },
      });

      expect(wrapper.find('.omnisearch__filter-text').text()).toEqual('Title contains "Epic"');
    });

    it('renders correctly for "not_contain" operator', () => {
      wrapper = shallowMount(FilterButton, {
        propsData: {
          fieldName: 'Title',
          dataType: DATATYPES.TEXT,
          operator: 'not_contain',
          value: 'Epic',
        },
      });

      expect(wrapper.find('.omnisearch__filter-text').text())
        .toEqual('Title does not contain "Epic"');
    });

    it('renders correctly for "equal" operator', () => {
      wrapper = shallowMount(FilterButton, {
        propsData: {
          fieldName: 'Title',
          dataType: DATATYPES.TEXT,
          operator: 'equals',
          value: 'something',
        },
      });

      expect(wrapper.find('.omnisearch__filter-text').text())
        .toEqual('Title equals "something"');
    });

    it('renders correctly for "not_equal" operator', () => {
      wrapper = shallowMount(FilterButton, {
        propsData: {
          fieldName: 'Title',
          dataType: DATATYPES.TEXT,
          operator: 'not_equals',
          value: 'something',
        },
      });

      expect(wrapper.find('.omnisearch__filter-text').text())
        .toEqual('Title does not equal "something"');
    });

    it('renders correctly for "gt" operator', () => {
      wrapper = shallowMount(FilterButton, {
        propsData: {
          fieldName: 'Rating',
          dataType: DATATYPES.NUMBER,
          operator: 'gt',
          value: 7,
        },
      });

      expect(wrapper.find('.omnisearch__filter-text').text())
        .toEqual('Rating greater than 7');
    });

    it('renders correctly for "gte" operator', () => {
      wrapper = shallowMount(FilterButton, {
        propsData: {
          fieldName: 'Rating',
          dataType: DATATYPES.NUMBER,
          operator: 'gte',
          value: 7,
        },
      });

      expect(wrapper.find('.omnisearch__filter-text').text())
        .toEqual('Rating greater than or equal 7');
    });

    it('renders correctly for "lt" operator', () => {
      wrapper = shallowMount(FilterButton, {
        propsData: {
          fieldName: 'Rating',
          dataType: DATATYPES.NUMBER,
          operator: 'lt',
          value: 5,
        },
      });

      expect(wrapper.find('.omnisearch__filter-text').text())
        .toEqual('Rating less than 5');
    });

    it('renders correctly for "lte" operator', () => {
      wrapper = shallowMount(FilterButton, {
        propsData: {
          fieldName: 'Rating',
          dataType: DATATYPES.NUMBER,
          operator: 'lte',
          value: 5,
        },
      });

      expect(wrapper.find('.omnisearch__filter-text').text())
        .toEqual('Rating less than or equal 5');
    });

    it('renders correctly for "equal" operator (List)', () => {
      wrapper = shallowMount(FilterButton, {
        propsData: {
          fieldName: 'Tags',
          dataType: DATATYPES.LIST,
          operator: 'equals',
          value: 'Item B',
        },
      });

      expect(wrapper.find('.omnisearch__filter-text').text())
        .toEqual('Tags equals Item B');
    });

    // in
    // not_in
  });

  describe('remove filter button', () => {
    it('should emit "remove-filter" event when clicked', async () => {
      wrapper.find('.omnisearch__remove-filter-btn').trigger('click');

      expect(wrapper.emitted('remove-filter').length).toBe(1);
    });
  });
});
