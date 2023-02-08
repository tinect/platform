// / <reference types="Cypress" />

import SettingsPageObject from '../../../../support/pages/module/sw-settings.page-object';

describe('Integration: crud integrations', () => {
    beforeEach(() => {
        cy.openInitialPage(`${Cypress.env('admin')}#/sw/dashboard/index`);
        cy.get('.sw-skeleton').should('not.exist');
        cy.get('.sw-loader').should('not.exist');
    });

    it('@settings: can create a new integration', { tags: ['pa-system-settings'] }, () => {
        // Request we want to wait for later
        cy.intercept({
            url: `${Cypress.env('apiPath')}/integration`,
            method: 'POST',
        }).as('createIntegration');

        // go to integration module
        cy.get('.sw-admin-menu__item--sw-settings').click();
        cy.get('.sw-settings__tab-system').click();
        cy.get('#sw-integration').click();

        // go to create page
        cy.get('.sw-integration-list__add-integration-action').click();

        // clear old data and type another one in name field
        cy.get('#sw-field--currentIntegration-label')
            .clear()
            .type('chat-key');

        cy.get('.sw-integration-detail-modal__save-action').click();

        // Verify create a integration
        cy.wait('@createIntegration').its('response.statusCode').should('equal', 204);

        cy.contains('.sw-data-grid__cell-content a[href="#"]', 'chat-key');
    });

    it('@settings: can create a new integration with double click', { tags: ['pa-system-settings'] }, () => {
        // Request we want to wait for later
        cy.intercept({
            url: `${Cypress.env('apiPath')}/integration`,
            method: 'POST',
        }).as('createIntegration');

        // go to integration module
        cy.get('.sw-admin-menu__item--sw-settings').click();
        cy.get('.sw-settings__tab-system').click();
        cy.get('#sw-integration').click();

        // go to create page
        cy.get('.sw-integration-list__add-integration-action').dblclick();

        // clear old data and type another one in name field
        cy.get('#sw-field--currentIntegration-label')
            .clear()
            .type('chat-key');

        cy.get('.sw-integration-detail-modal__save-action').click();

        // Verify create a integration
        cy.wait('@createIntegration').its('response.statusCode').should('equal', 204);

        cy.contains('.sw-data-grid__cell-content a[href="#"]', 'chat-key');
    });

    it('@settings: can edit a integration', { tags: ['pa-system-settings'] }, () => {
        const page = new SettingsPageObject();
        // Request we want to wait for later
        cy.intercept({
            url: `${Cypress.env('apiPath')}/integration`,
            method: 'POST',
        }).as('createIntegration');

        cy.intercept({
            url: `${Cypress.env('apiPath')}/integration/*`,
            method: 'PATCH',
        }).as('editIntegration');

        // go to integration module
        cy.get('.sw-admin-menu__item--sw-settings').click();
        cy.get('.sw-settings__tab-system').click();
        cy.get('#sw-integration').click();

        // go to create page
        cy.get('.sw-integration-list__add-integration-action').click();

        // clear old data and type another one in name field
        cy.get('#sw-field--currentIntegration-label')
            .clear()
            .type('chat-key');

        cy.get('.sw-integration-detail-modal__save-action').click();

        // Verify create a integration
        cy.wait('@createIntegration').its('response.statusCode').should('equal', 204);

        // click on the first element in grid
        cy.get(`${page.elements.dataGridRow}--0`).contains('chat-key').click();

        cy.get('#sw-field--currentIntegration-label')
            .clear()
            .type('chat-key-edited');

        cy.get('.sw-button--danger').click();

        cy.get('.sw-integration-detail-modal__save-action').click();

        // Verify edit a integration
        cy.wait('@editIntegration').its('response.statusCode').should('equal', 204);
    });

    it('@settings: can delete a integration', { tags: ['pa-system-settings'] }, () => {
        const page = new SettingsPageObject();
        // Request we want to wait for later
        cy.intercept({
            url: `${Cypress.env('apiPath')}/integration`,
            method: 'POST',
        }).as('createIntegration');

        cy.intercept({
            url: `${Cypress.env('apiPath')}/integration/*`,
            method: 'delete',
        }).as('deleteIntegration');

        // go to integration module
        cy.get('.sw-admin-menu__item--sw-settings').click();
        cy.get('.sw-settings__tab-system').click();
        cy.get('#sw-integration').click();

        // go to create page
        cy.get('.sw-integration-list__add-integration-action').click();

        // clear old data and type another one in name field
        cy.get('#sw-field--currentIntegration-label')
            .clear()
            .type('chat-key');

        cy.get('.sw-integration-detail-modal__save-action').click();

        // Verify create a integration
        cy.wait('@createIntegration').its('response.statusCode').should('equal', 204);
        cy.clickContextMenuItem(
            `${page.elements.contextMenu}-item--danger`,
            page.elements.contextMenuButton,
            `${page.elements.dataGridRow}--0`,
        );

        cy.contains('.sw-button--primary.sw-button--small span.sw-button__content', 'Delete').click();
        // Verify delete a integration
        cy.wait('@deleteIntegration').its('response.statusCode').should('equal', 204);
    });
});
