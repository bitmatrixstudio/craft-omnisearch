/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

module.exports = {
  plugins: [
    'cypress',
  ],
  env: {
    mocha: true,
    'cypress/globals': true,
  },
  rules: {
    strict: 'off',
  },
};
