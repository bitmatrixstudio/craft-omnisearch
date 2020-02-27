// https://docs.cypress.io/api/introduction/api.html

describe('Omnisearch.vue', () => {
  beforeEach(() => {
    cy.visit('/');

    cy.get('.omnisearch__add-filter-btn').as('addFilterBtn');
  });

  it('should render correctly initially', () => {
    cy.get('.omnisearch').should('have.length', 1);
    cy.get('.omnisearch__choose-fields').should('not.be.visible');

    cy.get('.omnisearch__active-filters').should('have.length', 0);
  });

  describe('Add Filter', () => {
    it('should show the add filter button initially', () => {
      cy.get('@addFilterBtn').should('have.length', 1).contains('Add Filter');
    });

    it('should open choose field menu when clicked', () => {
      cy.get('@addFilterBtn').click().then(() => {
        cy.get('@addFilterBtn').contains('Choose Field');

        cy.get('.omnisearch__choose-fields').should('be.visible');
      });
    });

    it('should close the choose field menu when clicked twice', () => {
      cy.get('@addFilterBtn').click().then(() => {
        cy.get('@addFilterBtn').contains('Choose Field');
        cy.get('.omnisearch__choose-fields').should('be.visible');
      });

      cy.get('@addFilterBtn').click().then(() => {
        cy.get('@addFilterBtn').contains('Add Filter');
        cy.get('.omnisearch__choose-fields').should('not.be.visible');
      });
    });
  });

  describe('Choose Fields Menu', () => {
    beforeEach(() => {
      cy.get('@addFilterBtn').click();
    });

    it('should autofocus on "search attributes" input when open', () => {
      cy.get('[data-test=fieldSearchInput]').should('have.focus');
    });

    it('should list the available fields sorted', () => {
      cy.get('[data-test=fieldListItem]').should('have.length', 3);
      cy.get('[data-test=fieldListItem]').eq(0).contains('Area');
      cy.get('[data-test=fieldListItem]').eq(1).contains('Post Date');
      cy.get('[data-test=fieldListItem]').eq(2).contains('Title');
    });

    it('should narrow down available fields when keyword is entered', () => {
      cy.get('[data-test=fieldSearchInput]').type('da').then(() => {
        cy.get('[data-test=fieldListItem]').should('have.length', 1);
        cy.get('[data-test=fieldListItem]').eq(0).contains('Post Date');
      });
    });

    describe('Choose Filter Method', () => {
      beforeEach(() => {
        cy.get('[data-test=fieldListItem]').eq(2).as('titleField');
        cy.get('@titleField').click();
      });

      it('should list filter methods field is clicked', () => {
        cy.get('@addFilterBtn').contains('Title');

        cy.get('[data-test=filterMethodListItem]').should('have.length', 7);
        cy.get('[data-test=filterMethodListItem]').eq(0).contains('contains');
        cy.get('[data-test=filterMethodListItem]').eq(1).contains('does not contain');
        cy.get('[data-test=filterMethodListItem]').eq(2).contains('equals');
        cy.get('[data-test=filterMethodListItem]').eq(3).contains('not equal to');
        cy.get('[data-test=filterMethodListItem]').eq(4).contains('starts with');
        cy.get('[data-test=filterMethodListItem]').eq(5).contains('is present');
        cy.get('[data-test=filterMethodListItem]').eq(6).contains('is not present');
      });

      describe('Filter method: "is_present"', () => {
        beforeEach(() => {
          cy.get('[data-test=filterMethodListItem]').eq(5).click();
        });

        it('should add filter when "is_present" filter method is chosen', () => {
          cy.get('@addFilterBtn').contains('Add Filter');
          cy.get('.omnisearch__choose-fields').should('not.be.visible');
          cy.get('[data-test=activeFilter]').should('have.length', 1);

          cy.get('[data-test=activeFilter]').eq(0).contains('Title is present');
        });
      });

      describe('Filter method: "contains"', () => {
        beforeEach(() => {
          cy.get('[data-test=filterMethodListItem]').eq(0).click();
        });

        it('should show text input when "contains" filter method is chosen', () => {
          cy.get('@addFilterBtn').contains('Title contains');
          cy.get('[data-test=activeFilter]').should('have.length', 0);

          cy.get('[data-test=compareValue]').should('be.visible');
          cy.get('[data-test=compareValueTextInput]').should('have.focus');

          cy.get('[data-test=applyFilterBtn]')
            .contains('Apply Filter')
            .should('be.disabled')
            .should('have.class', 'disabled');

          cy.get('[data-test=compareValueTextInput]').type('something');
          cy.get('[data-test=applyFilterBtn]').click().then(() => {
            cy.get('.omnisearch__choose-fields').should('not.be.visible');
            cy.get('[data-test=activeFilter]').should('have.length', 1);

            cy.get('[data-test=activeFilter]').eq(0).contains('Title contains "something"');

            cy.get('@addFilterBtn').should('have.text', '+ Add Filter');
          });
        });
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
      cy.get('@addFilterBtn').click();
      cy.get('[data-test=fieldListItem]').eq(0).click();
      cy.get('[data-test=filterMethodListItem]').eq(5).click();

      // Setup... Add a 'is present' filter for Title
      cy.get('@addFilterBtn').click();
      cy.get('[data-test=fieldListItem]').eq(2).click();
      cy.get('[data-test=filterMethodListItem]').eq(6).click();
    });

    it('should remove filter when close button is pressed', () => {
      cy.get('[data-test=activeFilter]').should('have.length', 2);
      cy.get('.omnisearch__filter:nth-child(2) .omnisearch__remove-filter-btn').click();
      cy.get('[data-test=activeFilter]').should('have.length', 1);
      cy.get('[data-test=activeFilter]').eq(0).contains('Area is present');

      cy.get('.omnisearch__filter:nth-child(1) .omnisearch__remove-filter-btn').click();
      cy.get('[data-test=activeFilter]').should('have.length', 0);
    });

    // test load existing filters
  });
});
