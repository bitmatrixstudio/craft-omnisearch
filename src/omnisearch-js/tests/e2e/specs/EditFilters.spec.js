describe('Edit filters', () => {
  beforeEach(() => {
    cy.visit('/');

    cy.get('[data-testid=add-filter] [data-testid=filter-button]').as('addFilterBtn');

    // Setup... Add a 'is present' filter for Rating
    cy.get('@addFilterBtn').click();
    cy.get('[data-testid=field-list-item-rating]').click();
    cy.get('[data-testid=filter-method-gte]').click();
    cy.get('[data-testid=compare-value-input]').type('4');
    cy.get('[data-testid=apply-filter-btn]').click();

    // Setup... Add a 'is present' filter for Title
    cy.get('@addFilterBtn').click();
    cy.get('[data-testid=field-list-item-title]').click();
    cy.get('[data-testid=filter-method-equals]').click();
    cy.get('[data-testid=compare-value-input]').type('something');
    cy.get('[data-testid=apply-filter-btn]').click();

    // Aliases
    cy.get('[data-testid="active-filter-0"] [data-testid=filter-button]').as('ratingFilter');
    cy.get('[data-testid="active-filter-1"] [data-testid=filter-button]').as('titleFilter');
  });

  it('should remove filter when close button is pressed', () => {
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
      cy.get('body').click(0, 0);

      cy.get('[data-testid="filter-panel"]').should('not.be.visible');
      cy.get('@titleFilter').contains('Title equals "something"');
    });

    // TODO: can change filter method
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
      cy.get('[data-testid="compare-value-input"]').clear().type('3');
      cy.get('[data-testid=apply-filter-btn]').click();
      cy.get('@ratingFilter').contains('Rating greater than or equal 3');
    });

    it('should cancel editing by clicking outside', () => {
      cy.get('[data-testid="compare-value-input"]').clear().type('2');
      cy.get('body').click(0, 0);

      cy.get('[data-testid="filter-panel"]').should('not.be.visible');
      cy.get('@ratingFilter').contains('Rating greater than or equal 4');
    });

    // TODO: can change filter method
    // TODO: decimals
  });

  // List Filters
  // Boolean Filters
  // Date Filters
});
