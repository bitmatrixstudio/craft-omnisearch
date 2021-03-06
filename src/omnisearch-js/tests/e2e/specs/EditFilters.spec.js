
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

describe('Edit filters', () => {
  beforeEach(() => {
    cy.visit('?rating[gte]=4&title[equals]=something&city[in]=2,4&isFeatured[equals]=false&postDate[date_before]=2021-09-15');

    cy.get('[data-testid=add-filter] [data-testid=filter-button]').as('addFilterBtn');

    // Aliases
    cy.get('[data-testid="active-filter-0"] [data-testid=filter-button]').as('ratingFilter');
    cy.get('[data-testid="active-filter-1"] [data-testid=filter-button]').as('titleFilter');
    cy.get('[data-testid="active-filter-2"] [data-testid=filter-button]').as('cityFilter');
    cy.get('[data-testid="active-filter-3"] [data-testid=filter-button]').as('isFeaturedFilter');
    cy.get('[data-testid="active-filter-4"] [data-testid=filter-button]').as('dateFilter');
  });

  it('should remove filter when close button is pressed', () => {
    cy.get('[data-testid^=active-filter]').should('have.length', 5);
    cy.get('[data-testid="active-filter-4"] [data-testid="remove-filter-button"]').click();
    cy.get('[data-testid^=active-filter]').should('have.length', 4);
    cy.get('[data-testid="active-filter-3"] [data-testid="remove-filter-button"]').click();
    cy.get('[data-testid^=active-filter]').should('have.length', 3);
    cy.get('[data-testid="active-filter-2"] [data-testid="remove-filter-button"]').click();

    cy.get('[data-testid^=active-filter]').should('have.length', 2);
    cy.get('[data-testid="active-filter-1"] [data-testid="remove-filter-button"]').click();

    cy.get('[data-testid^=active-filter]').should('have.length', 1);

    cy.get('[data-testid="active-filter-0"] [data-testid="remove-filter-button"]').click();
    cy.get('[data-testid^=active-filter]').should('have.length', 0);
  });

  describe('Edit: Text Filter', () => {
    beforeEach(() => {
      cy.get('@titleFilter').click();
    });

    it('should open filter panel when clicked', () => {
      cy.get('[data-testid="filter-panel"]').should('be.visible');
      cy.get('[data-testid="compare-value-input"]').should('have.focus');
      cy.get('[data-testid="compare-value-input"]').should('have.value', 'something');
    });

    it('should change the value of the text filter', () => {
      cy.get('[data-testid="compare-value-input"]').clear().type('hello world');
      cy.get('[data-testid=apply-filter-btn]').click();
      cy.get('@titleFilter').contains('Title equals "hello world"');
    });

    it('should cancel editing by clicking outside', () => {
      cy.get('[data-testid="compare-value-input"]').clear().type('hello world');
      cy.get('html').click('bottomLeft');
      cy.get('[data-testid="filter-panel"]').should('not.be.visible');
      cy.get('@titleFilter').contains('Title equals "something"');
    });

    it('can change filter method', () => {
      cy.get('[class="btn menubtn omnisearch__filter-method-dropdown"]').click();
      cy.get('[data-testid="filter-method-not_contain"]').click();
      cy.get('[data-testid="compare-value-input"]').clear().type('hello world');
      cy.get('[data-testid=apply-filter-btn]').click();
      cy.get('@titleFilter').contains('Title does not contain "hello world"');
    });
  });

  describe('Edit: Number Filter', () => {
    beforeEach(() => {
      cy.get('@ratingFilter').click();
    });

    it('should open filter panel when clicked', () => {
      cy.get('[data-testid="filter-panel"]').should('be.visible');
      cy.get('[data-testid="compare-value-input"]').should('have.focus');
      cy.get('[data-testid="compare-value-input"]').should('have.value', '4');
    });

    it('should change the value of the filter', () => {
      cy.get('[data-testid="compare-value-input"]').clear().clear().type('3');
      cy.get('[data-testid=apply-filter-btn]').click();
      cy.get('@ratingFilter').contains('Rating greater than or equal 3');
    });

    it('should cancel editing by clicking outside', () => {
      cy.get('[data-testid="compare-value-input"]').clear().clear().type('2');
      cy.get('html').click('bottomLeft');
      cy.get('[data-testid="filter-panel"]').should('not.be.visible');
      cy.get('@ratingFilter').contains('Rating greater than or equal 4');
    });

    it('change filter method', () => {
      cy.get('[class="btn menubtn omnisearch__filter-method-dropdown"]').click();
      cy.get('[data-testid="filter-method-lt"]').click();
      cy.get('[data-testid="compare-value-input"]').clear().clear().type('3');
      cy.get('[data-testid=apply-filter-btn]').click();
      cy.get('@ratingFilter').contains('Rating less than 3');
    });

    it('works with decimals', () => {
      cy.get('[data-testid="compare-value-input"]').clear().clear().type('2.5');
      cy.get('[data-testid=apply-filter-btn]').click();
      cy.get('@ratingFilter').contains('Rating greater than or equal 2.5');
    });
  });

  // List Filters
  describe('Edit: List Filter', () => {
    beforeEach(() => {
      cy.get('@cityFilter').click();
    });

    it('should open filter panel when clicked', () => {
      cy.get('[data-testid="filter-panel"]').should('be.visible');
    });

    it('should change the value of the filter', () => {
      cy.get('[data-testid=list-option] input[type=checkbox]').eq(1).click(); // unclick Johor Bahru
      cy.get('[data-testid=list-option] input[type=checkbox]').eq(0).click(); // Singapore
      cy.get('[data-testid=apply-filter-btn]').click();
      cy.get('@cityFilter').contains('Singapore');
      cy.get('@cityFilter').contains('Penang');
      cy.get('@cityFilter').should('not.contain', 'Johor Bahru');
    });
    it('change filter method', () => {
      cy.get('[class="btn menubtn omnisearch__filter-method-dropdown"]').click();
      cy.get('[data-testid="filter-method-equals"]').click();
      cy.get('[data-testid=list-option] input[type=radio]').eq(0).click(); // Singapore
      cy.get('[data-testid=apply-filter-btn]').click();
      cy.get('@cityFilter').contains('City equals Singapore');
    });

    it('should cancel editing by clicking outside', () => {
      cy.get('[data-testid=list-option] input[type=checkbox]').eq(0).click();
      cy.get('html').click('bottomLeft');
      cy.get('[data-testid="filter-panel"]').should('not.be.visible');
      cy.get('@cityFilter').contains('Johor Bahru');
      cy.get('@cityFilter').contains('Penang');
    });
  });

  // Boolean Filters
  describe('Edit: Boolean Filter', () => {
    beforeEach(() => {
      cy.get('@isFeaturedFilter').click();
    });

    it('should open filter panel when clicked', () => {
      cy.get('[data-testid="filter-panel"]').should('be.visible');
    });

    it('should change the value of the filter', () => {
      cy.get('[data-testid=compare-value-radio] input[type=radio]').eq(0).click();
      cy.get('[data-testid=apply-filter-btn]').click();
      cy.get('@isFeaturedFilter').contains('true', { matchCase: false });
    });

    it('should cancel editing by clicking outside', () => {
      cy.get('[data-testid=compare-value-radio] input[type=radio]').eq(1).click();
      cy.get('html').click('bottomLeft');
      cy.get('[data-testid="filter-panel"]').should('not.be.visible');
      cy.get('@isFeaturedFilter').contains('false', { matchCase: false });
    });

    it('change filter method', () => {
      cy.get('[class="btn menubtn omnisearch__filter-method-dropdown"]').click();
      cy.get('[data-testid="filter-method-is_present"]').click();
      cy.get('@isFeaturedFilter').contains('is present');
    });
  });

  // Date Filters
  describe('Edit: Date Filter', () => {
    beforeEach(() => {
      cy.get('@dateFilter').click();
    });

    it('should open filter panel when clicked', () => {
      cy.get('[data-testid="filter-panel"]').should('be.visible');
    });

    it('should change the value of the filter', () => {
      cy.get('[class="vc-weeks"]').contains('14').click();
      cy.get('[data-testid=apply-filter-btn]').click();
      cy.get('@dateFilter').contains('14');
    });

    it('should cancel editing by clicking outside', () => {
      cy.get('[class="vc-weeks"]').contains('14').click();
      cy.get('html').click('bottomLeft');
      cy.get('[data-testid="filter-panel"]').should('not.be.visible');
      cy.get('@dateFilter').contains('15');
    });
    it('change filter method', () => {
      cy.get('[class="btn menubtn omnisearch__filter-method-dropdown"]').click();
      cy.get('[data-testid="filter-method-date_after"]').click();
      cy.get('[class="vc-weeks"]').contains('16').click();
      cy.get('[data-testid=apply-filter-btn]').click();
      cy.get('@dateFilter').contains('after');
      cy.get('@dateFilter').contains('16');
      cy.get('@dateFilter').should('not.contain', 'before');
      cy.get('@dateFilter').should('not.contain', '15');
    });
  });
});
