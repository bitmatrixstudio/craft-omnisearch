describe('Active filters', () => {
  beforeEach(() => {
    cy.visit('/');

    cy.get('.omnisearch__add-filter-btn').as('addFilterBtn');

    // Setup... Add a 'is present' filter for Rating
    cy.get('@addFilterBtn').click();
    cy.get('[data-testid=field-list-item-rating]').click();
    cy.get('[data-testid=filter-method-is_present]').click();

    // Setup... Add a 'is present' filter for Title
    cy.get('@addFilterBtn').click();
    cy.get('[data-testid=field-list-item-title]').click();
    cy.get('[data-testid=filter-method-is_present]').click();
  });

  it('should remove filter when close button is pressed', () => {
    cy.get('[data-testid=active-filter]').should('have.length', 2);
    cy.get('.omnisearch__filter:nth-child(2) .omnisearch__remove-filter-btn').click();
    cy.get('[data-testid=active-filter]').should('have.length', 1);
    cy.get('[data-testid=active-filter]').eq(0).contains('Rating is present');

    cy.get('.omnisearch__filter:nth-child(1) .omnisearch__remove-filter-btn').click();
    cy.get('[data-testid=active-filter]').should('have.length', 0);
  });

  // test load existing filters
});
