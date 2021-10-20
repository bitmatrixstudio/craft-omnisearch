/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

import dayjs from 'dayjs';
import localizedFormat from 'dayjs/plugin/localizedFormat';
import { parseDateRange } from './utils';
import DATATYPES from './datatypes';

dayjs.extend(localizedFormat);

let translateFn = (text, params) => {
  const tokens = params || {};

  return Object.keys(tokens).reduce((str, token) => str.replaceAll(`{${token}}`, tokens[token] || ''), text);
};

export function setTranslateFn(fn) {
  translateFn = fn;
}

export function translate(text, params) {
  return translateFn(text, params);
}

export function formatDate(value) {
  return dayjs(value).format('ll');
}

export function formatDateRange(value) {
  const { start, end } = parseDateRange(value);

  return translate('{start} to {end}', {
    start: formatDate(start),
    end: formatDate(end),
  });
}

export function formatListItem(value, items = []) {
  const listOption = items.find((item) => item.value === value);

  return listOption != null ? listOption.label : null;
}

function formatValue(field, operator, value) {
  let label = value;
  if (field.items != null) {
    label = formatListItem(value, field.items);
  } else if (field.dataType === 'date') {
    label = operator === 'date_between' ? formatDateRange(value) : formatDate(value);
  }

  return label;
}

export function formatValues(field, operator, compareValue) {
  const values = !Array.isArray(compareValue) ? [compareValue] : compareValue;
  const valueText = values.map((val) => formatValue(field, operator, val)).join(', ');

  return field.dataType === DATATYPES.TEXT ? `"${valueText}"` : valueText;
}
