define(['jquery', 'TYPO3/CMS/Recordlist/LinkBrowser'], function ($, LinkBrowser) {

    'use strict';

    /**
     *
     * @type object
     * @exports TYPO3/CMS/Recordlist/LinkHandler
     */
    var LinkHandler = {};

    /**
     * TypoLink template
     *
     * @type {string}
     */
    var linkTemplate = '';

    /**
     *
     * @param {Event} event
     */
    LinkHandler.linkCurrent = function (event) {
        event.preventDefault();

        var $this = $(this);
        var value = linkTemplate.replace('###RECORD_UID###', $this.data('uid'));

        LinkBrowser.finalizeFunction(value);
    };

    $(function () {
        // Set typolink template
        linkTemplate = $('body').data('typolink-template');

        // Make search bar visible
        $('#db_list-searchbox-toolbar').show();

        // Catch click
        $('.recordlist table td span[data-table]').on('click', LinkHandler.linkCurrent);

        // Catch page click
        $('.t3js-pageLink').on('click', function (e) {
            e.preventDefault();

            $(this)
                .closest('.list-tree-group')
                .find('.list-tree-show')
                .trigger('click');
        })
    });

    return LinkHandler;
});
