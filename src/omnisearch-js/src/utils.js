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
