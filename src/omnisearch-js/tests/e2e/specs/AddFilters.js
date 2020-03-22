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
      cy.get('[data-test=fieldSearchInput]').should('have.focus');
    });

    it('should list the available fields sorted', () => {
      cy.get('[data-test=fieldListItem]').should('have.length', 5);
      cy.get('[data-test=fieldListItem]').eq(0).contains('Is Featured');
      cy.get('[data-test=fieldListItem]').eq(1).contains('Post Date');
      cy.get('[data-test=fieldListItem]').eq(2).contains('Rating');
      cy.get('[data-test=fieldListItem]').eq(3).contains('Tags');
      cy.get('[data-test=fieldListItem]').eq(4).contains('Title');
    });

    it('should narrow down available fields when keyword is entered', () => {
      cy.get('[data-test=fieldSearchInput]').type('da').then(() => {
        cy.get('[data-test=fieldListItem]').should('have.length', 1);
        cy.get('[data-test=fieldListItem]').eq(0).contains('Post Date');
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
    cy.get('[data-test=fieldListItem]').eq(4).as('titleField');
    cy.get('@titleField').click();

    cy.get('[data-test=filterMethodListItem]').eq(0).as('containsFilter');
    cy.get('[data-test=filterMethodListItem]').eq(1).as('notContainsFilter');
    cy.get('[data-test=filterMethodListItem]').eq(2).as('startsWithFilter');
    cy.get('[data-test=filterMethodListItem]').eq(3).as('equalsFilter');
    cy.get('[data-test=filterMethodListItem]').eq(4).as('notEqualsFilter');
    cy.get('[data-test=filterMethodListItem]').eq(5).as('isPresentFilter');
    cy.get('[data-test=filterMethodListItem]').eq(6).as('isNotPresentFilter');
  });

  it('should list the correct filter methods for text field', () => {
    cy.get('@addFilterBtn').contains('Title');

    cy.get('[data-test=filterMethodListItem]').should('have.length', 7);
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
      cy.get('[data-test=activeFilter]').should('have.length', 0);
    });

    it('should show compare value text input', () => {
      cy.get('[data-test=compareValue]').should('be.visible');
      cy.get('[data-test=compareValueInput]').should('have.focus');

      cy.get('[data-test=applyFilterBtn]')
        .contains('Apply Filter')
        .should('be.disabled')
        .should('have.class', 'disabled');
    });

    it('should set value when the "apply filter" button is clicked', () => {
      cy.get('[data-test=compareValueInput]').type('something');
      cy.get('[data-test=applyFilterBtn]').click().then(() => {
        cy.get('.omnisearch__choose-fields').should('not.be.visible');
        cy.get('[data-test=activeFilter]').should('have.length', 1);

        cy.get('[data-test=activeFilter]').eq(0).contains('Title contains "something"');

        cy.get('@addFilterBtn').should('have.text', '+ Add Filter');
      });
    });

    it('should set value with "enter" key', () => {
      cy.get('[data-test=compareValueInput]').type('something{enter}').then(() => {
        cy.get('.omnisearch__choose-fields').should('not.be.visible');
        cy.get('[data-test=activeFilter]').should('have.length', 1);

        cy.get('[data-test=activeFilter]').eq(0).contains('Title contains "something"');

        cy.get('@addFilterBtn').should('have.text', '+ Add Filter');
      });
    });
  });

  describe('Filter method: "not_contain"', () => {
    beforeEach(() => {
      cy.get('@notContainsFilter').click();

      cy.get('@addFilterBtn').contains('Title does not contain');
      cy.get('[data-test=activeFilter]').should('have.length', 0);
    });

    it('should show compare value text input', () => {
      cy.get('[data-test=compareValue]').should('be.visible');
      cy.get('[data-test=compareValueInput]').should('have.focus');

      cy.get('[data-test=applyFilterBtn]')
        .contains('Apply Filter')
        .should('be.disabled')
        .should('have.class', 'disabled');
    });
  });

  describe('Filter method: "starts_with"', () => {
    beforeEach(() => {
      cy.get('@startsWithFilter').click();

      cy.get('@addFilterBtn').contains('Title starts with');
      cy.get('[data-test=activeFilter]').should('have.length', 0);
    });

    it('should show compare value text input', () => {
      cy.get('[data-test=compareValue]').should('be.visible');
      cy.get('[data-test=compareValueInput]').should('have.focus');

      cy.get('[data-test=applyFilterBtn]')
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
      cy.get('[data-test=compareValue]').should('be.visible');
      cy.get('[data-test=compareValueInput]').should('have.focus');
    });

    it('should set value when the "apply filter" button is clicked', () => {
      cy.get('[data-test=compareValueInput]').type('something');
      cy.get('[data-test=applyFilterBtn]').click().then(() => {
        cy.get('.omnisearch__choose-fields').should('not.be.visible');
        cy.get('[data-test=activeFilter]').should('have.length', 1);

        cy.get('[data-test=activeFilter]').eq(0).contains('Title equals "something"');

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
      cy.get('[data-test=compareValue]').should('be.visible');
      cy.get('[data-test=compareValueInput]').should('have.focus');
    });
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

  describe('Filter method: "is_not_present"', () => {
    beforeEach(() => {
      cy.get('[data-test=filterMethodListItem]').eq(6).click();
    });

    it('should add filter when "is_present" filter method is chosen', () => {
      cy.get('@addFilterBtn').contains('Add Filter');
      cy.get('.omnisearch__choose-fields').should('not.be.visible');
      cy.get('[data-test=activeFilter]').should('have.length', 1);

      cy.get('[data-test=activeFilter]').eq(0).contains('Title is not present');
    });
  });
});

describe('Number Filters', () => {
  beforeEach(() => {
    cy.get('@addFilterBtn').click();
    cy.get('[data-test=fieldListItem]').eq(2).as('ratingField');
    cy.get('@ratingField').click();

    cy.get('[data-test=filterMethodListItem]').eq(0).as('equalsFilter');
    cy.get('[data-test=filterMethodListItem]').eq(1).as('notEqualsFilter');
    cy.get('[data-test=filterMethodListItem]').eq(2).as('gtFilter');
    cy.get('[data-test=filterMethodListItem]').eq(3).as('gteFilter');
    cy.get('[data-test=filterMethodListItem]').eq(4).as('ltFilter');
    cy.get('[data-test=filterMethodListItem]').eq(5).as('lteFilter');
    cy.get('[data-test=filterMethodListItem]').eq(6).as('isPresentFilter');
    cy.get('[data-test=filterMethodListItem]').eq(7).as('isNotPresentFilter');
  });

  it('should list the correct filter methods for numeric field', () => {
    cy.get('@addFilterBtn').contains('Rating');

    cy.get('[data-test=filterMethodListItem]').should('have.length', 8);
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
      cy.get('[data-test=compareValue]').should('be.visible');
      cy.get('[data-test=compareValueInput]').should('have.focus');
      cy.get('[data-test=compareValueInput]').should('have.attr', 'type', 'number');
    });

    it('should set value when the "apply filter" button is clicked', () => {
      cy.get('[data-test=compareValueInput]').type('7.2');
      cy.get('[data-test=applyFilterBtn]').click().then(() => {
        cy.get('.omnisearch__choose-fields').should('not.be.visible');
        cy.get('[data-test=activeFilter]').should('have.length', 1);

        cy.get('[data-test=activeFilter]').eq(0).contains('Rating equals 7.2');

        cy.get('@addFilterBtn').should('have.text', '+ Add Filter');
      });
    });
  });

  describe('Filter method: "not_equals"', () => {
    beforeEach(() => {
      cy.get('@notEqualsFilter').click();
    });

    it('should show compare value number input', () => {
      cy.get('[data-test=compareValue]').should('be.visible');
      cy.get('[data-test=compareValueInput]').should('have.focus');
      cy.get('[data-test=compareValueInput]').should('have.attr', 'type', 'number');
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
      cy.get('[data-test=compareValue]').should('be.visible');
      cy.get('[data-test=compareValueInput]').should('have.focus');
      cy.get('[data-test=compareValueInput]').should('have.attr', 'type', 'number');
    });

    it('should set value when the "apply filter" button is clicked', () => {
      cy.get('[data-test=compareValueInput]').type('7.2');
      cy.get('[data-test=applyFilterBtn]').click().then(() => {
        cy.get('[data-test=activeFilter]').eq(0).contains('Rating greater than 7.2');
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
      cy.get('[data-test=compareValue]').should('be.visible');
      cy.get('[data-test=compareValueInput]').should('have.focus');
      cy.get('[data-test=compareValueInput]').should('have.attr', 'type', 'number');
    });

    it('should set value when the "apply filter" button is clicked', () => {
      cy.get('[data-test=compareValueInput]').type('7.2');
      cy.get('[data-test=applyFilterBtn]').click().then(() => {
        cy.get('[data-test=activeFilter]').eq(0).contains('Rating greater than or equal 7.2');
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
      cy.get('[data-test=compareValue]').should('be.visible');
      cy.get('[data-test=compareValueInput]').should('have.focus');
      cy.get('[data-test=compareValueInput]').should('have.attr', 'type', 'number');
    });

    it('should set value when the "apply filter" button is clicked', () => {
      cy.get('[data-test=compareValueInput]').type('7.2');
      cy.get('[data-test=applyFilterBtn]').click().then(() => {
        cy.get('[data-test=activeFilter]').eq(0).contains('Rating less than 7.2');
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
      cy.get('[data-test=compareValue]').should('be.visible');
      cy.get('[data-test=compareValueInput]').should('have.focus');
      cy.get('[data-test=compareValueInput]').should('have.attr', 'type', 'number');
    });

    it('should set value when the "apply filter" button is clicked', () => {
      cy.get('[data-test=compareValueInput]').type('7.2');
      cy.get('[data-test=applyFilterBtn]').click().then(() => {
        cy.get('[data-test=activeFilter]').eq(0).contains('Rating less than or equal 7.2');
      });
    });
  });
});

describe('Boolean Filters', () => {
  beforeEach(() => {
    cy.get('@addFilterBtn').click();
    cy.get('[data-test=fieldListItem]').eq(0).as('isFeaturedField');
    cy.get('@isFeaturedField').click();

    cy.get('[data-test=filterMethodListItem]').eq(0).as('equalsFilter');
    cy.get('[data-test=filterMethodListItem]').eq(1).as('notEqualsFilter');
    cy.get('[data-test=filterMethodListItem]').eq(2).as('isPresentFilter');
    cy.get('[data-test=filterMethodListItem]').eq(3).as('isNotPresentFilter');
  });

  it('shows 4 filter types', () => {
    cy.get('[data-test=filterMethodListItem]').should('have.length', 4);

    cy.get('[data-test=filterMethodListItem]').eq(0).contains('equals');
    cy.get('[data-test=filterMethodListItem]').eq(1).contains('does not equal');
    cy.get('[data-test=filterMethodListItem]').eq(2).contains('is present');
    cy.get('[data-test=filterMethodListItem]').eq(3).contains('is not present');
  });

  describe('Filter method: "equals"', () => {
    beforeEach(() => {
      cy.get('@equalsFilter').click();
    });

    it('shows true or false options', () => {
      cy.get('[data-test=compareValue]').should('be.visible');
      cy.get('[data-test=compareValueRadio]').eq(0).contains('True');
      cy.get('[data-test=compareValueRadio]').eq(1).contains('False');
    });

    it('should set value when the "apply filter" button is clicked', () => {
      cy.get('[data-test=compareValueRadio] input[type=radio]').eq(1).click();
      cy.get('[data-test=applyFilterBtn]').click().then(() => {
        cy.get('[data-test=activeFilter]').eq(0).contains('Is Featured equals false');
      });
    });
  });

  describe('Filter method: "not_equals"', () => {
    beforeEach(() => {
      cy.get('@notEqualsFilter').click();
    });

    it('shows true or false options', () => {
      cy.get('[data-test=compareValue]').should('be.visible');
      cy.get('[data-test=compareValueRadio]').eq(0).contains('True');
      cy.get('[data-test=compareValueRadio]').eq(1).contains('False');
    });

    it('should set value when the "apply filter" button is clicked', () => {
      cy.get('[data-test=compareValueRadio] input[type=radio]').eq(0).click();
      cy.get('[data-test=applyFilterBtn]').click().then(() => {
        cy.get('[data-test=activeFilter]').eq(0).contains('Is Featured does not equal true');
      });
    });
  });
});

describe('List Filters', () => {
  beforeEach(() => {
    cy.get('@addFilterBtn').click();
    cy.get('[data-test=fieldListItem]').eq(3).as('tagsField');
    cy.get('@tagsField').click();

    cy.get('[data-test=filterMethodListItem]').eq(0).as('includesFilter');
    cy.get('[data-test=filterMethodListItem]').eq(1).as('doesNotIncludeFilter');
    cy.get('[data-test=filterMethodListItem]').eq(2).as('equalsFilter');
    cy.get('[data-test=filterMethodListItem]').eq(3).as('notEqualsFilter');
    cy.get('[data-test=filterMethodListItem]').eq(4).as('isFilter');
    cy.get('[data-test=filterMethodListItem]').eq(5).as('isNotFilter');
  });

  it('shows the correct filter methods', () => {
    cy.get('[data-test=filterMethodListItem]').should('have.length', 6);

    cy.get('[data-test=filterMethodListItem]').eq(0).contains('includes');
    cy.get('[data-test=filterMethodListItem]').eq(1).contains('does not include');
    cy.get('[data-test=filterMethodListItem]').eq(2).contains('equals');
    cy.get('[data-test=filterMethodListItem]').eq(3).contains('does not equal');
    cy.get('[data-test=filterMethodListItem]').eq(4).contains('is present');
    cy.get('[data-test=filterMethodListItem]').eq(5).contains('is not present');
  });

  describe.only('Filter method "equals"', () => {
    beforeEach(() => {
      cy.get('@equalsFilter').click();
    });

    it('shows the list item available items in a checklist', () => {
      cy.get('[data-test=listOptions]').should('be.visible');
      cy.get('[data-test=listOption] input[type=radio]').should('have.length', 5);
    });

    it('should set value when the "apply filter" button is clicked', () => {
      cy.get('[data-test=listOption] input[type=radio]').eq(1).click();

      cy.get('[data-test=applyFilterBtn]').click().then(() => {
        cy.get('[data-test=activeFilter]').eq(0).contains('Tags equals Item B');
      });
    });
  });

  describe('Filter method "in"', () => {
    beforeEach(() => {
      cy.get('@equalsFilter').click();
    });

    it('shows the list item available items in a checklist', () => {
      cy.get('[data-test=listOptions]').should('be.visible');
      cy.get('[data-test=listOption] input[type=radio]').should('have.length', 5);
    });

    it('should set value when the "apply filter" button is clicked', () => {
      cy.get('[data-test=listOption] input[type=radio]').eq(1).click();

      cy.get('[data-test=applyFilterBtn]').click().then(() => {
        cy.get('[data-test=activeFilter]').eq(0).contains('Tags equals Item B');
      });
    });
  });
});

describe('Date Filters', () => {

});
