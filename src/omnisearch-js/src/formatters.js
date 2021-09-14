import dayjs from 'dayjs';
import localizedFormat from 'dayjs/plugin/localizedFormat';
import { parseDateRange } from './utils';

dayjs.extend(localizedFormat);

export function formatDate(value) {
  return dayjs(value).format('ll');
}

export function formatDateRange(value) {
  const { start, end } = parseDateRange(value);

  return `${formatDate(start)} to ${formatDate(end)}`;
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

export function formatValues(field, operator, values) {
  return values.map((val) => formatValue(field, operator, val)).join(', ');
}
