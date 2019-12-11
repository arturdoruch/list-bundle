/*!
 * (c) 2019 Artur Doruch <arturdoruch@interia.pl>
 */

define([
    'arturdoruchJs/component/eventManager',
    'arturdoruchJs/component/Form/Form'
], function(em, Form) {

    var Class = function (formSelector) {
        this._formSelector = formSelector;
    };

    Class.prototype = {
        /**
         * @param {jQuery} $listContainer
         */
        attachEvent: function ($listContainer) {
            var $form = $listContainer.find(this._formSelector);

            if ($form.length === 0) {
                return;
            }

            var form = new Form($form[0]);
            $form.find('select').removeAttr('onchange');

            em.on('change', $form, function () {
                em.dispatch('arturdoruch_list.update', [form.createRequestUrl()]);
            });
        }
    };

    return Class;
});