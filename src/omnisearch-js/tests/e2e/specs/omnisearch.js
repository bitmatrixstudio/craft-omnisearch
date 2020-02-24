// https://docs.cypress.io/api/introduction/api.html

describe('Omnisearch.vue', () => {
  beforeEach(() => {
    cy.visit('/');
  });

  it('should render correctly initially', () => {
    cy.get('.omnisearch').should('have.length', 1);
    cy.get('.omnisearch__choose-fields').should('not.be.visible');

    cy.get('.omnisearch__active-filters').should('have.length', 0);
  });

  describe('Add Filter', () => {
    it('should show the add filter button initially', () => {
      cy.get('.omnisearch__add-filter-btn').should('have.length', 1).contains('Add Filter');
    });

    it('should open choose field menu when clicked', () => {
      cy.get('.omnisearch__add-filter-btn').click().then(() => {
        cy.get('.omnisearch__add-filter-btn').contains('Choose Field');

        cy.get('.omnisearch__choose-fields').should('be.visible');
      });
    });

    it('should close the choose field menu when clicked twice', () => {
      cy.get('.omnisearch__add-filter-btn').click().then(() => {
        cy.get('.omnisearch__add-filter-btn').contains('Choose Field');
        cy.get('.omnisearch__choose-fields').should('be.visible');
      });

      cy.get('.omnisearch__add-filter-btn').click().then(() => {
        cy.get('.omnisearch__add-filter-btn').contains('Add Filter');
        cy.get('.omnisearch__choose-fields').should('not.be.visible');
      });
    });
  });

  describe('Choose Fields Menu', () => {
    beforeEach(() => {
      cy.get('.omnisearch__add-filter-btn').click();
    });

    it('should autofocus on "search attributes" input when open', () => {
      cy.get('.omnisearch__field-list-search > input').should('have.focus');
    });

    it('should list the available fields sorted', () => {
      cy.get('.omnisearch__field-list > .omnisearch__field-list-item').should('have.length', 3);
      cy.get('.omnisearch__field-list-item').eq(0).contains('Area');
      cy.get('.omnisearch__field-list-item').eq(1).contains('Post Date');
      cy.get('.omnisearch__field-list-item').eq(2).contains('Title');
    });

    it('should narrow down available fields when keyword is entered', () => {
      cy.get('.omnisearch__field-list-search > input').type('da').then(() => {
        cy.get('.omnisearch__field-list > .omnisearch__field-list-item').should('have.length', 1);
        cy.get('.omnisearch__field-list-item').eq(0).contains('Post Date');
      });
    });

    describe('Choose filter method', () => {
      beforeEach(() => {
        cy.get('.omnisearch__field-list-item').eq(2).as('titleField');
      });

      it('should list filter methods field is clicked', () => {
        cy.get('@titleField').click();
        cy.get('.omnisearch__add-filter-btn').contains('Title');

        cy.get('.omnisearch__filter-methods > .omnisearch__filter-method').should('have.length', 7);
        cy.get('.omnisearch__filter-method').eq(0).contains('contains');
        cy.get('.omnisearch__filter-method').eq(1).contains('does not contain');
        cy.get('.omnisearch__filter-method').eq(2).contains('equals');
        cy.get('.omnisearch__filter-method').eq(3).contains('not equal to');
        cy.get('.omnisearch__filter-method').eq(4).contains('starts with');
        cy.get('.omnisearch__filter-method').eq(5).contains('is present');
        cy.get('.omnisearch__filter-method').eq(6).contains('is not present');
      });

      it('should add filter when filter method is chosen', () => {
        cy.get('@titleField').click();
        cy.get('.omnisearch__filter-method').eq(5).click();

        cy.get('.omnisearch__add-filter-btn').contains('Add Filter');
        cy.get('.omnisearch__choose-fields').should('not.be.visible');

        cy.get('.omnisearch__active-filters > .omnisearch__filter').should('have.length', 1);

        cy.get('.omnisearch__active-filters > .omnisearch__filter').eq(0).contains('Title is present');
      });

      // contains filter...
      // equals filter...
      // not equals filter...
      // starts with...
      // is present...
      // is notpresent...
    });
  });

  describe('Active filters', () => {
    beforeEach(() => {
      // Setup... Add a 'is present' filter for Area
      cy.get('.omnisearch__add-filter-btn').click();
      cy.get('.omnisearch__field-list-item').eq(0).click();
      cy.get('.omnisearch__filter-method').eq(5).click();

      // Setup... Add a 'is present' filter for Title
      cy.get('.omnisearch__add-filter-btn').click();
      cy.get('.omnisearch__field-list-item').eq(2).click();
      cy.get('.omnisearch__filter-method').eq(6).click();
    });

    it('should remove filter when close button is pressed', () => {
      cy.get('.omnisearch__active-filters > .omnisearch__filter').should('have.length', 2);
      cy.get('.omnisearch__filter:nth-child(2) .omnisearch__remove-filter-btn').click();
      cy.get('.omnisearch__active-filters > .omnisearch__filter').should('have.length', 1);
      cy.get('.omnisearch__active-filters > .omnisearch__filter').eq(0).contains('Area is present');

      cy.get('.omnisearch__filter:nth-child(1) .omnisearch__remove-filter-btn').click();
      cy.get('.omnisearch__active-filters > .omnisearch__filter').should('have.length', 0);
    });

    // test load existing filters
  });
});
