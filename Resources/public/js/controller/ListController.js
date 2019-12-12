/*!
 * (c) 2019 Artur Doruch <arturdoruch@interia.pl>
 */

define([
    'arturdoruchJs/component/eventManager',
    'arturdoruchJs/component/ajax',
    'arturdoruchJs/util/urlUtils',
    './FormController',
    './LinkController'
], function(em, ajax, urlUtils, FormController, LinkController) {

    var defaultOptions = {
        gettingItemsMessage: null,
        gettingItemsLoader: true,
        paginationListSelector: 'ul.ad-list__pagination',
        sortLinkSelector: 'a.ad-list__sort-link',
        limitFormSelector: 'form[name="ad-list__limit"]',
        sortFormSelector: 'form[name="ad-list__sort"]',
        addHistoryState: true
    };

    /**
     * @param {object} listContainer Table container element
     * @param {FilterFormController} [filterFormController]
     * @param {object} [options] Update list items options.
     * @param {string} [options.gettingItemsMessage = null] Message showing while getting list items.
     * @param {bool}   [options.gettingItemsLoader = true] Whether the image loader should be displayed while getting list items.
     * @param {string}   [options.paginationListSelector]
     * @param {string}   [options.limitFormSelector]
     * @param {string}   [options.sortLinkSelector]
     * @param {string}   [options.sortFormSelector]
     * @param {boolean}  [options.addHistoryState = true] Whether to add list state to the browser session history stack, after ajax request.
     */
    var ListController = function(listContainer, filterFormController, options) {
        var self = this;

        if (!listContainer) {
            throw new Error('ListController: Missing listContainer argument.');
        }

        this.$listContainer = $(listContainer);
        this.options = $.extend(defaultOptions, options);
        this.updateListeners = [];

        this._limitFormController = new FormController(this.options.limitFormSelector);
        this._sortFormController = new FormController(this.options.sortFormSelector);
        this._paginationLinkController = new LinkController(this.options.paginationListSelector + ' a');
        this._sortLinkController = new LinkController(this.options.sortLinkSelector);

        this._attachListeners(self);
        this._attachEvents();

        window.onpopstate = function(e) {
            self._updateList(e.state, window.location.href);
        };
    };

    ListController.prototype = {
        /**
         * Registers listener called after update list.
         *
         * @param {function} listener The listener function. Function receives argument: $listContainer.
         */
        addUpdateListListener: function (listener) {
            if (typeof listener !== 'function') {
                throw new Error('The update list listener is not a function.');
            }

            this.updateListeners.push(listener);
        },

        _attachListeners: function(self) {
            em.addListener('arturdoruch_list.update', function(event, url) {
                self._getAndUpdateList(url);
            });
        },

        _attachEvents: function() {
            this._paginationLinkController.attachEvent(this.$listContainer);
            this._sortLinkController.attachEvent(this.$listContainer);
            this._limitFormController.attachEvent(this.$listContainer);
            this._sortFormController.attachEvent(this.$listContainer);
        },

        /**
         * Loads table content. Makes ajax request and update table with returned data.
         *
         * @param {string} url Url to table content resource, is uses for ajax request.
         */
        _getAndUpdateList: function(url) {
            var self = this;

            ajax.send(url, this.options.gettingItemsMessage, this.options.gettingItemsLoader)
                .done(function(html) {
                    if (self.options.addHistoryState === true) {
                        addHistoryState(url, html);
                    }
                    self._updateList(html);
                })
                .fail(function (response) {
                    console.log(response.status + ' ' + response.statusText);
                });
        },

        _updateList: function(html) {
            this.$listContainer.html(html);
            this._attachEvents();
            // Dispatch update table event
            for (var l in this.updateListeners) {
                this.updateListeners[l].call(null, this.$listContainer);
            }
        }
    };

    function addHistoryState(url, html) {
        history.pushState(html, '', url);
    }

    return ListController;
});