/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

import dayjs from 'dayjs';
import localizedFormat from 'dayjs/plugin/localizedFormat';

dayjs.extend(localizedFormat);

export function parseDateRange(value = '') {
  let start = null;
  let end = null;

  if (value && value.length > 0) {
    const [from = '', to = ''] = value.split(',');
    if (from.length > 0) {
      start = dayjs(from).toDate();
    }

    if (to.length > 0) {
      end = dayjs(to).toDate();
    }
  }

  return {
    start,
    end,
  };
}

export function createQueryParams(filters) {
  return filters.map((filter) => {
    const values = ['in', 'not_in'].includes(filter.operator) ? filter.value.join(',') : filter.value;

    return `${filter.field}[${filter.operator}]=${values}`;
  }).join('&');
}

export function parseQueryParams(url) {
  const location = new URL(url);
  const queryParams = new URLSearchParams(location.search);

  return Array.from(queryParams.entries()).map(([key, val]) => {
    const [, field, operator] = key.match(/([\w/.:]+)\[(\w+)]/);

    const value = ['in', 'not_in'].includes(operator) ? val.split(',') : val;

    return {
      field,
      operator,
      value,
    };
  });
}
