/*!
 * (c) 2019 Artur Doruch <arturdoruch@interia.pl>
 */

define([
    'js/component/eventManager',
    'js/component/ajax',
    'js/util/urlUtils',
    './FormController'
], function(em, ajax, urlUtils, FormController) {

    var defaultOptions = {
        gettingItemsMessage: null,
        gettingItemsLoader: true,
        paginationListSelector: 'ul.ad-list__pagination',
        limitFormSelector: 'form[name="ad-list__limit"]',
        sortLinkSelector: 'a.ad-list__sort-link',
        sortFormSelector: 'form[name="ad-list__sort"]'
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
     *
     */
    var ListController = function(listContainer, filterFormController, options) {
        var self = this;

        this.$listContainer = $(listContainer);
        this.options = $.extend(defaultOptions, options);
        this.updateListeners = [];

        this.limitFormController = new FormController(this.options.limitFormSelector);
        this.sortFormController = new FormController(this.options.sortFormSelector);

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
            var self = this;
            var selectorList = [this.options.paginationListSelector + ' a', this.options.sortLinkSelector];

            for (var i in selectorList) {
                em.on('click', this.$listContainer.find(selectorList[i]), function (e) {
                    self._getAndUpdateList(e.target.href);
                });
            }

            this.limitFormController.attachEvent(this.$listContainer);
            this.sortFormController.attachEvent(this.$listContainer);
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
                    if (location.pathname === urlUtils.parseUrl(url).pathname) {
                        setLocation(url, html);
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

    /**
     * Sets history state.
     */
    function setLocation(url, html) {
        history.pushState(html, '', url);
    }

    return ListController;
});