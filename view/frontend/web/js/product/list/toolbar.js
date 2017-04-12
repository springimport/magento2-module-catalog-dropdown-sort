define([
    'jquery',
    'jquery/ui',
    'Magento_Catalog/js/product/list/toolbar'
], function ($) {
    /**
     * ProductListToolbarForm Widget - this widget is setting cookie and submitting form according to toolbar controls
     */
    $.widget('mage.productListToolbarForm', $.mage.productListToolbarForm, {
        _create: function () {
            console.log(Math.random() * 100);
            var orderParams = [
                {paramName: this.options.direction, default: this.options.directionDefault},
                {paramName: this.options.order, default: this.options.orderDefault}
            ];

            this._bind($(this.options.modeControl), this.options.mode, this.options.modeDefault);
            //this._bind($(this.options.directionControl), this.options.direction, this.options.directionDefault);
            //this._bind($(this.options.orderControl), this.options.order, this.options.orderDefault);
            this._bind($(this.options.limitControl), this.options.limit, this.options.limitDefault);

            this._bindMultiParams($(this.options.orderControl), orderParams);
        },
        _bindMultiParams: function (element, params) {
            if (element.is("select")) {
                element.on('change', params, $.proxy(this._processSelectMultiParams, this));
            } else {
                element.on('click', params, $.proxy(this._processLinkMultiParams, this));
            }
        },
        _processLinkMultiParams: function (event) {
            event.preventDefault();
            /*this.changeUrl(
                event.data.paramName,
                $(event.currentTarget).data('value'),
                event.data.default
            );*/
        },

        _processSelectMultiParams: function (event) {
            console.log(event.data);
            /*this.changeUrl(
                event.data.paramName,
                event.currentTarget.options[event.currentTarget.selectedIndex].value,
                event.data.default
            );*/
        },
        changeUrlByMultiParams: function (params) {
            var params = params || [];
            var decode = window.decodeURIComponent;
            var urlPaths = this.options.url.split('?'),
                baseUrl = urlPaths[0],
                urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                paramData = {},
                parameters;
            for (var i = 0; i < urlParams.length; i++) {
                parameters = urlParams[i].split('=');
                paramData[decode(parameters[0])] = parameters[1] !== undefined
                    ? decode(parameters[1].replace(/\+/g, '%20'))
                    : '';
            }
            if (params.length) {
                for (var key in params) {
                    var item = params[key];
                    if (item.defaultValue !== item.paramValue) {
                        paramData[item.paramName] = item.paramValue;
                    }
                }
            }
            paramData = $.param(paramData);
            location.href = baseUrl + (paramData.length ? '?' + paramData : '');
        },
    });

    return $.mage.productListToolbarForm;
});
