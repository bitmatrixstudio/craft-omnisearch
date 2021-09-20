// https://docs.cypress.io/api/introduction/api.html

beforeEach(() => {
  cy.visit('/');

  cy.get('.omnisearch__add-filter-btn').as('addFilterBtn');
});

describe('Add Filter', () => {
  it('should render correctly initially', () => {
    cy.get('.omnisearch').should('have.length', 1);
    cy.get('.omnisearch__choose-fields').should('not.be.visible');

    cy.get('.omnisearch__active-filters').should('have.length', 0);
  });

  it('should show the add filter button initially', () => {
    cy.get('@addFilterBtn').should('have.length', 1).contains('Add Filter');
  });

  it('should open choose field menu when clicked', () => {
    cy.get('@addFilterBtn').click().then(() => {
      cy.get('@addFilterBtn').contains('Choose Field');

      cy.get('.omnisearch__choose-fields').should('be.visible');
    });
  });

  describe('Choose Fields Menu', () => {
    beforeEach(() => {
      cy.get('@addFilterBtn').click();
    });

    it('should autofocus on "search attributes" input when open', () => {
      cy.get('[data-testid=field-search-input]').should('have.focus');
    });

    it('should list the available fields sorted', () => {
      cy.get('[data-testid^=field-list-item]').should('have.length', 5);

      const expectedItems = [
        'City',
        'Is Featured',
        'Post Date',
        'Rating',
        'Title',
      ];

      expectedItems.forEach((item, i) => {
        cy.get('[data-testid^=field-list-item]').eq(i).contains(item);
      });
    });

    it('should narrow down available fields when keyword is entered', () => {
      cy.get('[data-testid=field-search-input]').type('da').then(() => {
        cy.get('[data-testid^=field-list-item]').should('have.length', 1);
        cy.get('[data-testid^=field-list-item]').eq(0).contains('Post Date');
      });
    });

    it('should close the menu when button is clicked again', () => {
      cy.get('@addFilterBtn').click().then(() => {
        cy.get('@addFilterBtn').contains('Add Filter');
        cy.get('.omnisearch__choose-fields').should('not.be.visible');
      });
    });

    it('should close the menu when click outside', () => {
      cy.get('html').click(500, 500).then(() => {
        cy.get('@addFilterBtn').contains('Add Filter');
        cy.get('.omnisearch__choose-fields').should('not.be.visible');
      });
    });
  });
});

describe('Text Filters', () => {
  beforeEach(() => {
    cy.get('@addFilterBtn').click();
    cy.get('[data-testid=field-list-item-title]').as('titleField');
    cy.get('@titleField').click();

    cy.get('[data-testid=filter-method-contain]').as('containsFilter');
    cy.get('[data-testid=filter-method-not_contain]').as('notContainsFilter');
    cy.get('[data-testid=filter-method-starts_with]').as('startsWithFilter');
    cy.get('[data-testid=filter-method-equals]').as('equalsFilter');
    cy.get('[data-testid=filter-method-not_equals]').as('notEqualsFilter');
    cy.get('[data-testid=filter-method-is_present]').as('isPresentFilter');
    cy.get('[data-testid=filter-method-is_not_present]').as('isNotPresentFilter');
  });

  it('should list the correct filter methods for text field', () => {
    cy.get('@addFilterBtn').contains('Title');

    cy.get('[data-testid^=filter-method]').should('have.length', 7);
    cy.get('@containsFilter').contains('contains');
    cy.get('@notContainsFilter').contains('does not contain');
    cy.get('@startsWithFilter').contains('starts with');
    cy.get('@equalsFilter').contains('equals');
    cy.get('@notEqualsFilter').contains('does not equal');
    cy.get('@isPresentFilter').contains('is present');
    cy.get('@isNotPresentFilter').contains('is not present');
  });

  describe('Filter method: "contain"', () => {
    beforeEach(() => {
      cy.get('@containsFilter').click();

      cy.get('@addFilterBtn').contains('Title contains');
      cy.get('[data-testid=active-filter]').should('have.length', 0);
    });

    it('should show compare value text input', () => {
      cy.get('[data-testid=compareValue]').should('be.visible');
      cy.get('[data-testid=compare-value-input]').should('have.focus');

      cy.get('[data-testid=applyFilterBtn]')
        .contains('Apply Filter')
        .should('be.disabled')
        .should('have.class', 'disabled');
    });

    it('should set value when the "apply filter" button is clicked', () => {
      cy.get('[data-testid=compare-value-input]').type('something');
      cy.get('[data-testid=applyFilterBtn]').click().then(() => {
        cy.get('.omnisearch__choose-fields').should('not.be.visible');
        cy.get('[data-testid=active-filter]').should('have.length', 1);

        cy.get('[data-testid=active-filter]').eq(0).contains('Title contains "something"');

        cy.get('@addFilterBtn').should('have.text', '+ Add Filter');
      });
    });

    it('should set value with "enter" key', () => {
      cy.get('[data-testid=compare-value-input]').type('something{enter}').then(() => {
        cy.get('.omnisearch__choose-fields').should('not.be.visible');
        cy.get('[data-testid=active-filter]').should('have.length', 1);

        cy.get('[data-testid=active-filter]').eq(0).contains('Title contains "something"');

        cy.get('@addFilterBtn').should('have.text', '+ Add Filter');
      });
    });
  });

  describe('Filter method: "not_contain"', () => {
    beforeEach(() => {
      cy.get('@notContainsFilter').click();

      cy.get('@addFilterBtn').contains('Title does not contain');
      cy.get('[data-testid=active-filter]').should('have.length', 0);
    });

    it('should show compare value text input', () => {
      cy.get('[data-testid=compareValue]').should('be.visible');
      cy.get('[data-testid=compare-value-input]').should('have.focus');

      cy.get('[data-testid=applyFilterBtn]')
        .contains('Apply Filter')
        .should('be.disabled')
        .should('have.class', 'disabled');
    });
  });

  describe('Filter method: "starts_with"', () => {
    beforeEach(() => {
      cy.get('@startsWithFilter').click();

      cy.get('@addFilterBtn').contains('Title starts with');
      cy.get('[data-testid=active-filter]').should('have.length', 0);
    });

    it('should show compare value text input', () => {
      cy.get('[data-testid=compareValue]').should('be.visible');
      cy.get('[data-testid=compare-value-input]').should('have.focus');

      cy.get('[data-testid=applyFilterBtn]')
        .contains('Apply Filter')
        .should('be.disabled')
        .should('have.class', 'disabled');
    });
  });

  describe('Filter method: "equals"', () => {
    beforeEach(() => {
      cy.get('@equalsFilter').click();
    });

    it('should change add filter button text', () => {
      cy.get('@addFilterBtn').contains('Title equals');
    });

    it('should show compare value text input', () => {
      cy.get('[data-testid=compareValue]').should('be.visible');
      cy.get('[data-testid=compare-value-input]').should('have.focus');
    });

    it('should set value when the "apply filter" button is clicked', () => {
      cy.get('[data-testid=compare-value-input]').type('something');
      cy.get('[data-testid=applyFilterBtn]').click().then(() => {
        cy.get('.omnisearch__choose-fields').should('not.be.visible');
        cy.get('[data-testid=active-filter]').should('have.length', 1);

        cy.get('[data-testid=active-filter]').eq(0).contains('Title equals "something"');

        cy.get('@addFilterBtn').should('have.text', '+ Add Filter');
      });
    });
  });

  describe('Filter method: "not_equals"', () => {
    beforeEach(() => {
      cy.get('@notEqualsFilter').click();
    });

    it('should change add filter button text', () => {
      cy.get('@addFilterBtn').contains('Title does not equal');
    });

    it('should show compare value text input', () => {
      cy.get('[data-testid=compareValue]').should('be.visible');
      cy.get('[data-testid=compare-value-input]').should('have.focus');
    });
  });

  describe('Filter method: "is_present"', () => {
    beforeEach(() => {
      cy.get('[data-testid=filter-method-is_present]').click();
    });

    it('should add filter when "is_present" filter method is chosen', () => {
      cy.get('@addFilterBtn').contains('Add Filter');
      cy.get('.omnisearch__choose-fields').should('not.be.visible');
      cy.get('[data-testid=active-filter]').should('have.length', 1);

      cy.get('[data-testid=active-filter]').eq(0).contains('Title is present');
    });
  });

  describe('Filter method: "is_not_present"', () => {
    beforeEach(() => {
      cy.get('[data-testid=filter-method-is_not_present]').click();
    });

    it('should add filter when "is_present" filter method is chosen', () => {
      cy.get('@addFilterBtn').contains('Add Filter');
      cy.get('.omnisearch__choose-fields').should('not.be.visible');
      cy.get('[data-testid=active-filter]').should('have.length', 1);

      cy.get('[data-testid=active-filter]').eq(0).contains('Title is not present');
    });
  });
});

describe('Number Filters', () => {
  beforeEach(() => {
    cy.get('@addFilterBtn').click();
    cy.get('[data-testid=field-list-item-rating]').as('ratingField');
    cy.get('@ratingField').click();

    cy.get('[data-testid=filter-method-equals]').as('equalsFilter');
    cy.get('[data-testid=filter-method-not_equals]').as('notEqualsFilter');
    cy.get('[data-testid=filter-method-gt]').as('gtFilter');
    cy.get('[data-testid=filter-method-gte]').as('gteFilter');
    cy.get('[data-testid=filter-method-lt]').as('ltFilter');
    cy.get('[data-testid=filter-method-lte]').as('lteFilter');
    cy.get('[data-testid=filter-method-is_present]').as('isPresentFilter');
    cy.get('[data-testid=filter-method-is_not_present]').as('isNotPresentFilter');
  });

  it('should list the correct filter methods for numeric field', () => {
    cy.get('@addFilterBtn').contains('Rating');

    cy.get('[data-testid^=filter-method]').should('have.length', 8);
    cy.get('@equalsFilter').contains('equals');
    cy.get('@notEqualsFilter').contains('does not equal');
    cy.get('@gtFilter').contains('greater than');
    cy.get('@gteFilter').contains('greater than or equal');
    cy.get('@ltFilter').contains('less than');
    cy.get('@lteFilter').contains('less than or equal');
    cy.get('@isPresentFilter').contains('is present');
    cy.get('@isNotPresentFilter').contains('is not present');
  });

  describe('Filter method: "equals"', () => {
    beforeEach(() => {
      cy.get('@equalsFilter').click();
    });

    it('should show compare value number input', () => {
      cy.get('[data-testid=compareValue]').should('be.visible');
      cy.get('[data-testid=compare-value-input]').should('have.focus');
      cy.get('[data-testid=compare-value-input]').should('have.attr', 'type', 'number');
    });

    it('should set value when the "apply filter" button is clicked', () => {
      cy.get('[data-testid=compare-value-input]').type('7.2');
      cy.get('[data-testid=applyFilterBtn]').click().then(() => {
        cy.get('.omnisearch__choose-fields').should('not.be.visible');
        cy.get('[data-testid=active-filter]').should('have.length', 1);

        cy.get('[data-testid=active-filter]').eq(0).contains('Rating equals 7.2');

        cy.get('@addFilterBtn').should('have.text', '+ Add Filter');
      });
    });
  });

  describe('Filter method: "not_equals"', () => {
    beforeEach(() => {
      cy.get('@notEqualsFilter').click();
    });

    it('should show compare value number input', () => {
      cy.get('[data-testid=compareValue]').should('be.visible');
      cy.get('[data-testid=compare-value-input]').should('have.focus');
      cy.get('[data-testid=compare-value-input]').should('have.attr', 'type', 'number');
    });
  });

  describe('Filter method: "gt"', () => {
    beforeEach(() => {
      cy.get('@gtFilter').click();
    });

    it('should change add filter button text', () => {
      cy.get('@addFilterBtn').contains('Rating greater than');
    });

    it('should show compare value number input', () => {
      cy.get('[data-testid=compareValue]').should('be.visible');
      cy.get('[data-testid=compare-value-input]').should('have.focus');
      cy.get('[data-testid=compare-value-input]').should('have.attr', 'type', 'number');
    });

    it('should set value when the "apply filter" button is clicked', () => {
      cy.get('[data-testid=compare-value-input]').type('7.2');
      cy.get('[data-testid=applyFilterBtn]').click().then(() => {
        cy.get('[data-testid=active-filter]').eq(0).contains('Rating greater than 7.2');
      });
    });
  });

  describe('Filter method: "gte"', () => {
    beforeEach(() => {
      cy.get('@gteFilter').click();
    });

    it('should change add filter button text', () => {
      cy.get('@addFilterBtn').contains('Rating greater than or equal');
    });

    it('should show compare value number input', () => {
      cy.get('[data-testid=compareValue]').should('be.visible');
      cy.get('[data-testid=compare-value-input]').should('have.focus');
      cy.get('[data-testid=compare-value-input]').should('have.attr', 'type', 'number');
    });

    it('should set value when the "apply filter" button is clicked', () => {
      cy.get('[data-testid=compare-value-input]').type('7.2');
      cy.get('[data-testid=applyFilterBtn]').click().then(() => {
        cy.get('[data-testid=active-filter]').eq(0).contains('Rating greater than or equal 7.2');
      });
    });
  });

  describe('Filter method: "lt"', () => {
    beforeEach(() => {
      cy.get('@ltFilter').click();
    });

    it('should change add filter button text', () => {
      cy.get('@addFilterBtn').contains('Rating less');
    });

    it('should show compare value number input', () => {
      cy.get('[data-testid=compareValue]').should('be.visible');
      cy.get('[data-testid=compare-value-input]').should('have.focus');
      cy.get('[data-testid=compare-value-input]').should('have.attr', 'type', 'number');
    });

    it('should set value when the "apply filter" button is clicked', () => {
      cy.get('[data-testid=compare-value-input]').type('7.2');
      cy.get('[data-testid=applyFilterBtn]').click().then(() => {
        cy.get('[data-testid=active-filter]').eq(0).contains('Rating less than 7.2');
      });
    });
  });

  describe('Filter method: "lte"', () => {
    beforeEach(() => {
      cy.get('@lteFilter').click();
    });

    it('should change add filter button text', () => {
      cy.get('@addFilterBtn').contains('Rating less than or equal');
    });

    it('should show compare value number input', () => {
      cy.get('[data-testid=compareValue]').should('be.visible');
      cy.get('[data-testid=compare-value-input]').should('have.focus');
      cy.get('[data-testid=compare-value-input]').should('have.attr', 'type', 'number');
    });

    it('should set value when the "apply filter" button is clicked', () => {
      cy.get('[data-testid=compare-value-input]').type('7.2');
      cy.get('[data-testid=applyFilterBtn]').click().then(() => {
        cy.get('[data-testid=active-filter]').eq(0).contains('Rating less than or equal 7.2');
      });
    });
  });
});

describe('Boolean Filters', () => {
  beforeEach(() => {
    cy.get('@addFilterBtn').click();
    cy.get('[data-testid=field-list-item-isFeatured]').as('isFeaturedField');
    cy.get('@isFeaturedField').click();

    cy.get('[data-testid=filter-method-equals]').as('equalsFilter');
    cy.get('[data-testid=filter-method-is_present]').as('isPresentFilter');
    cy.get('[data-testid=filter-method-is_not_present]').as('isNotPresentFilter');
  });

  it('shows 3 filter types', () => {
    cy.get('[data-testid^=filter-method]').should('have.length', 3);

    cy.get('[data-testid=filter-method-equals]').contains('equals');
    cy.get('[data-testid=filter-method-is_present]').contains('is present');
    cy.get('[data-testid=filter-method-is_not_present]').contains('is not present');
  });

  describe('Filter method: "equals"', () => {
    beforeEach(() => {
      cy.get('@equalsFilter').click();
    });

    it('shows true or false options', () => {
      cy.get('[data-testid=compareValue]').should('be.visible');
      cy.get('[data-testid=compareValueRadio]').eq(0).contains('True');
      cy.get('[data-testid=compareValueRadio]').eq(1).contains('False');
    });

    it('should set value when the "apply filter" button is clicked', () => {
      cy.get('[data-testid=compareValueRadio] input[type=radio]').eq(1).click();
      cy.get('[data-testid=applyFilterBtn]').click().then(() => {
        cy.get('[data-testid=active-filter]').eq(0).contains('Is Featured equals false');
      });
    });
  });
});

describe('List Filters', () => {
  beforeEach(() => {
    cy.get('@addFilterBtn').click();
    cy.get('[data-testid=field-list-item-city]').eq(0).as('cityField');
    cy.get('@cityField').click();

    cy.get('[data-testid=filter-method-in]').as('includesFilter');
    cy.get('[data-testid=filter-method-not_in]').as('doesNotIncludeFilter');
    cy.get('[data-testid=filter-method-equals]').as('equalsFilter');
    cy.get('[data-testid=filter-method-not_equals]').as('notEqualsFilter');
    cy.get('[data-testid=filter-method-is_present]').as('isFilter');
    cy.get('[data-testid=filter-method-is_not_present]').as('isNotFilter');
  });

  it('shows the correct filter methods', () => {
    cy.get('[data-testid^=filter-method]').should('have.length', 6);

    cy.get('@includesFilter').should('exist');
    cy.get('@doesNotIncludeFilter').should('exist');
    cy.get('@equalsFilter').should('exist');
    cy.get('@notEqualsFilter').should('exist');
    cy.get('@isFilter').should('exist');
    cy.get('@isNotFilter').should('exist');
  });

  describe('Filter method "equals"', () => {
    beforeEach(() => {
      cy.get('@equalsFilter').click();
    });

    it('shows the list item available items in a checklist', () => {
      cy.get('[data-testid=list-options]').should('be.visible');
      cy.get('[data-testid=list-option] input[type=radio]').should('have.length', 22);
    });

    it('shows filtered options when keyword is entered', () => {
      cy.get('[data-testid=list-options-filter-input]').should('have.focus');
      cy.get('[data-testid=list-options-filter-input]').type('to').then(() => {
        cy.get('[data-testid=list-option]').should('have.length', 2);
        cy.get('[data-testid=list-option]').eq(0).contains('Tokyo');
        cy.get('[data-testid=list-option]').eq(1).contains('Kyoto');
      });
    });

    it('should set value when the "apply filter" button is clicked', () => {
      cy.get('[data-testid=list-option] input[type=radio]').eq(1).click();
      cy.get('@addFilterBtn').contains('City equals Johor Bahru');

      cy.get('[data-testid=applyFilterBtn]').click().then(() => {
        cy.get('[data-testid=active-filter]').eq(0).contains('City equals Johor Bahru');
      });
    });
  });

  describe('Filter method "in"', () => {
    beforeEach(() => {
      cy.get('@includesFilter').click();
    });

    it('should show "City includes" initially', () => {
      cy.get('@addFilterBtn').contains('City includes');
    });

    it('shows the list item available items in a checklist', () => {
      cy.get('[data-testid=list-options]').should('be.visible');
      cy.get('[data-testid=list-option] input[type=checkbox]').should('have.length', 22);
    });

    it('shows filtered options when keyword is entered', () => {
      cy.get('[data-testid=list-options-filter-input]').should('have.focus');
      cy.get('[data-testid=list-options-filter-input]').type('to').then(() => {
        cy.get('[data-testid=list-option]').should('have.length', 2);
      });
    });

    it('should set value when the "apply filter" button is clicked', () => {
      cy.get('[data-testid=list-options-filter-input]').type('to').then(() => {
        cy.get('[data-testid=list-option] input[type=checkbox]').eq(0).click();
        cy.get('[data-testid=list-option] input[type=checkbox]').eq(1).click();

        cy.get('[data-testid=applyFilterBtn]').click().then(() => {
          cy.get('[data-testid=active-filter]').eq(0).contains('City includes Tokyo, Kyoto');
        });
      });
    });
  });

  describe('Filter method "not_in"', () => {
    beforeEach(() => {
      cy.get('@doesNotIncludeFilter').click();
    });

    it('should show "City does not include" initially', () => {
      cy.get('@addFilterBtn').contains('City does not include');
    });

    it('should set value when the "apply filter" button is clicked', () => {
      cy.get('[data-testid=list-option] input[type=checkbox]').eq(4).click();
      cy.get('@addFilterBtn').contains('City does not include Melbourne');

      cy.get('[data-testid=list-option] input[type=checkbox]').eq(5).click();
      cy.get('@addFilterBtn').contains('City does not include Melbourne, Sydney');

      cy.get('[data-testid=list-option] input[type=checkbox]').eq(6).click();
      cy.get('@addFilterBtn').contains('City does not include Melbourne, Sydney, Perth');

      cy.get('[data-testid=list-option] input[type=checkbox]').eq(7).click();
      cy.get('@addFilterBtn').contains('City does not include Melbourne, Sydney, Perth, New York');

      cy.get('[data-testid=list-option] input[type=checkbox]').eq(8).click();
      cy.get('@addFilterBtn').contains('City does not include Melbourne, Sydney, Perth, New York, London');

      cy.get('[data-testid=applyFilterBtn]').click().then(() => {
        cy.get('[data-testid=active-filter]')
          .eq(0)
          .contains('City does not include Melbourne, Sydney, Perth, New York, London');
      });
    });
  });
});

describe('Date Filters', () => {

});
