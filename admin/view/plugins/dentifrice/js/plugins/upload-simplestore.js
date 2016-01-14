// Copyright (c) 2015 Oasiswork.
// All Rights Reserved.
//
// This Source Code Form is subject to the
// terms of the Mozilla Public License, v. 2.0.
// If a copy of the MPL was not distributed with this file,
// You can obtain one at
// http://mozilla.org/MPL/2.0/.

var uploadStore = (function ($) {
    'use strict';

    var doUpload = function (data, width) {
        var ret = null;

        $.ajax({
            url: settings.uploadURL,
            method: 'POST',
            data: data,
            async: false,
            // With files, its better if jquery does not try
            // to manage form content
            cache: false,
            contentType: false,
            processData: false,
            type: 'json'
        })
                .done(function (json) {

                    if (json.url != undefined) {
                        info('file uploaded. Response: ' + json.url);
                        ret = json.url;
                    } else {
                        warn('Failed uploading file: ');
                    }
                })
                .fail(function (jqXHR, textStatus) {

                    warn('Failed uploading file: ' + textStatus);

                });

        return ret;
    };

    return {
        doUpload: doUpload
    };

})(jQuery);
