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
    operator: 'starts_with',
    label: 'starts with',
    dataTypes: [DATATYPES.TEXT],
  },
  {
    operator: 'equals',
    label: 'equals',
    dataTypes: [DATATYPES.TEXT, DATATYPES.NUMBER],
  },
  {
    operator: 'not_equals',
    label: 'does not equal',
    dataTypes: [DATATYPES.TEXT, DATATYPES.NUMBER],
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
    dataTypes: [DATATYPES.TEXT, DATATYPES.NUMBER],
  },
  {
    operator: 'is_not_present',
    label: 'is not present',
    requiresValue: false,
    dataTypes: [DATATYPES.TEXT, DATATYPES.NUMBER],
  },
];
