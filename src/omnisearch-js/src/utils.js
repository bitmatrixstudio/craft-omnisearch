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
    let values = ['in', 'not_in'].includes(filter.operator) ? filter.value.join(',') : filter.value;
    values = values === undefined ? '' : values;

    return `${filter.field}[${filter.operator}]=${values}`;
  }).join('&');
}

export function parseQueryParams(url) {
  const location = new URL(url);
  const queryParams = new URLSearchParams(location.search);

  return Array.from(queryParams.entries()).map(([key, val]) => {
    const [, field, operator] = key.match(/([\w/.:]+)\[(\w+)]/);

    let value = ['in', 'not_in'].includes(operator) ? val.split(',') : val;
    value = value === '' ? undefined : value;

    return {
      field,
      operator,
      value,
    };
  });
}

export function waitFor(fn, timeout = 5000) {
  return new Promise((resolve, reject) => {
    const startTime = new Date().getTime();

    const intervalID = setInterval(() => {
      const elapsed = new Date().getTime() - startTime;
      const done = fn();

      if (done) {
        clearInterval(intervalID);
        resolve();
      }

      if (elapsed > timeout) {
        clearInterval(intervalID);
        reject();
      }
    }, 100);
  });
}
