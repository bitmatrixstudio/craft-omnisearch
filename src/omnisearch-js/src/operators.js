import DATATYPES from './datatypes';

export default [
  {
    operator: 'contain',
    label: 'contains',
    dataTypes: [DATATYPES.TEXT],
  },
  {
    operator: 'not_contain',
    label: 'does not contain',
    dataTypes: [DATATYPES.TEXT],
  },
  {
    operator: 'in',
    label: 'includes',
    dataTypes: [DATATYPES.LIST],
    multiple: true,
  },
  {
    operator: 'not_in',
    label: 'does not include',
    dataTypes: [DATATYPES.LIST],
    multiple: true,
  },
  {
    operator: 'starts_with',
    label: 'starts with',
    dataTypes: [DATATYPES.TEXT],
  },
  {
    operator: 'equals',
    label: 'equals',
    dataTypes: [DATATYPES.TEXT, DATATYPES.NUMBER, DATATYPES.BOOLEAN, DATATYPES.LIST],
  },
  {
    operator: 'not_equals',
    label: 'does not equal',
    dataTypes: [DATATYPES.TEXT, DATATYPES.NUMBER, DATATYPES.LIST],
  },
  {
    operator: 'gt',
    label: 'greater than',
    dataTypes: [DATATYPES.NUMBER],
  },
  {
    operator: 'gte',
    label: 'greater than or equal',
    dataTypes: [DATATYPES.NUMBER],
  },
  {
    operator: 'lt',
    label: 'less than',
    dataTypes: [DATATYPES.NUMBER],
  },

  {
    operator: 'lte',
    label: 'less than or equal',
    dataTypes: [DATATYPES.NUMBER],
  },
  {
    operator: 'is_present',
    label: 'is present',
    requiresValue: false,
    dataTypes: [
      DATATYPES.TEXT,
      DATATYPES.NUMBER,
      DATATYPES.BOOLEAN,
      DATATYPES.LIST,
    ],
  },
  {
    operator: 'is_not_present',
    label: 'is not present',
    requiresValue: false,
    dataTypes: [
      DATATYPES.TEXT,
      DATATYPES.NUMBER,
      DATATYPES.BOOLEAN,
      DATATYPES.LIST,
    ],
  },
  {
    operator: 'date_between',
    label: 'is between',
    dataTypes: [DATATYPES.DATE],
  },
  {
    operator: 'date_before',
    label: 'is before',
    dataTypes: [DATATYPES.DATE],
  },
  {
    operator: 'date_after',
    label: 'is after',
    dataTypes: [DATATYPES.DATE],
  },
];
