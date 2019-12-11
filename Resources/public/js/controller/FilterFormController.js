/*!
 * (c) 2019 Artur Doruch <arturdoruch@interia.pl>
 */

define([
    'arturdoruchJs/component/eventManager',
    'arturdoruchJs/component/Form/Form',
    'arturdoruchJs/util/urlUtils'
], function(em, Form, urlUtils) {

    var defaultOptions = {
        filterButtonSelector: 'button[type="submit"]',
        resetButtonSelector: 'button[type="reset"]',
        resetData: {},
        noResetFields: [],
        filterAfterReset: true
    };

    /**
     * @constructor
     *
     * @param {string} formSelector
     * @param {{}} [options]
     * @param {string} [filterButtonSelector]
     * @param {string} [resetButtonSelector]
     * @param {{}      [resetData]
     * @param {[]}     [noResetFields] The names of form elements which values should not be reset when reset button is clicked.
     * @param {bool}   [filterAfterReset = true]
     */
    var Class = function (formSelector, options) {
        this.form = new Form(formSelector);
        this.options = $.extend(defaultOptions, options);
        this.queryParameterNames = $('div[data-query-parameter-names]').data('queryParameterNames');

        for (var name in this.queryParameterNames) {
            this.form.removeElement(this.queryParameterNames[name]);
        }

        var attachEvents = function(self) {
            var options = {
                context: self
            };

            self.form
                .addElementListener('change', 'select:not([multiple="multiple"])', self._filter, options)
                .addElementListener('change', 'input[type="radio"]', self._filter, options)
                .addElementListener('click', self.options.filterButtonSelector, self._filter, options)
                .addElementListener('click', self.options.resetButtonSelector, self._reset, options)
                .addSubmitListener(self._filter, options);
        };

        attachEvents(this);
    };

    Class.prototype = {
        _filter: function() {
            var urlParameters = urlUtils.parseQueryString(location.search);

            delete urlParameters[this.form.getName()];
            delete urlParameters[this.queryParameterNames.page];

            var url = this.form.createRequestUrl(true, urlParameters);

            em.dispatch('arturdoruch_list.update', [url]);
        },

        _reset: function() {
            this.form.resetData(this.options.noResetFields, false);
            this.form.setData(this.options.resetData);

            if (this.options.filterAfterReset === true) {
                this._filter();
            }
        }
    };

    return Class;
});