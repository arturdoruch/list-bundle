
define(['js/component/eventManager'], function(em) {

    var Class = function (linkSelector) {
        this._linkSelector = linkSelector;
    };

    Class.prototype = {
        /**
         * @param {jQuery} $listContainer
         */
        attachEvent: function ($listContainer) {
            em.on('click', $listContainer.find(this._linkSelector), function (e) {
                em.dispatch('arturdoruch_list.update', [e.target.href]);
            });
        }
    };

    return Class;
});