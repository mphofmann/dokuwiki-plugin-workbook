var WbCookie = { // TODO GPL
    data: {},
    name: 'wb_cookie_prefs',
    /* -------------------------------------------------------------------- */
    setValue: function (key, val) {
        var text = [],
            _this = this;
        this.init();
        this.data[key] = val;

        //save the whole data array
        jQuery.each(_this.data, function (key, val) {
            if (_this.data.hasOwnProperty(key)) {
                text.push(encodeURIComponent(key) + '#' + encodeURIComponent(val));
            }
        });
        jQuery.cookie(this.name, text.join('#'), {expires: 365});
    },
    /* -------------------------------------------------------------------- */
    getValue: function (key) {
        this.init();
        return this.data[key];
    },
    /* -------------------------------------------------------------------- */
    init: function () {
        var text, parts, i;
        if (!jQuery.isEmptyObject(this.data)) {
            return;
        }
        text = jQuery.cookie(this.name);
        if (text) {
            parts = text.split('#');
            for (i = 0; i < parts.length; i += 2) {
                this.data[decodeURIComponent(parts[i])] = decodeURIComponent(parts[i + 1]);
            }
        }
    }
    /* -------------------------------------------------------------------- */
};