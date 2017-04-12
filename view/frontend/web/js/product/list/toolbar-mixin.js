define([
    'jquery',
    'mage/translate'
], function ($) {

    return function (widget) {
        $.widget('mage.productListToolbarForm', widget, {
            orderDefault: 'name',
            _create: function () {
                var orderParams = [
                    {paramName: this.options.order, default: this.options.orderDefault},
                    {paramName: this.options.direction, default: this.options.directionDefault}
                ];
                this._bind($(this.options.modeControl), this.options.mode, this.options.modeDefault);
                this._bind($(this.options.limitControl), this.options.limit, this.options.limitDefault);
                this._bindMultiParams($(this.options.orderControl), orderParams);
            },

            _bindMultiParams: function (element, params) {
                if (element.is("select")) {
                    element.on('change', params, $.proxy(this._processSelectMultiParams, this));
                } else {
                    /* TODO: implement _processLinkMultiParams() */
                }
            },

            _processSelectMultiParams: function (event) {
                var data = event.data;
                var value = event.currentTarget.options[event.currentTarget.selectedIndex].value;
                var valuesList = value.toLowerCase().split('-');

                if (data.length == valuesList.length) {
                    for (var key in data) {
                        data[key].paramValue = valuesList[key];
                    }
                }

                this.changeUrlByMultiParams(data);
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
                        if (item.paramValue !== undefined && item.defaultValue !== item.paramValue) {
                            paramData[item.paramName] = item.paramValue;
                        }
                    }
                }
                paramData = $.param(paramData);
                location.href = baseUrl + (paramData.length ? '?' + paramData : '');
            }
        });
        return $.mage.productListToolbarForm;
    };
});