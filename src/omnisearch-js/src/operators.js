export default [
  { operator: 'contain', label: 'contains' },
  { operator: 'not_contain', label: 'does not contain' },
  { operator: 'equals', label: 'equals' },
  { operator: 'not_equals', label: 'does not equal' },
  { operator: 'starts_with', label: 'starts with' },
  { operator: 'is_present', label: 'is present', requiresValue: false },
  { operator: 'is_not_present', label: 'is not present', requiresValue: false },
];
