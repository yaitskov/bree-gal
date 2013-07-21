dynamicJsInitHooks.push(function (root) {
                            root.find('#fileupload').fileupload(
                                {
                                    url: '/gallery/uploadFile/'
                                },
                                'option', {
                                    disableImageResize: false,
                                    maxFileSize: 50000000,
                                    acceptFileTypes: /.*([jJ][pP][eE]?[gG]|[Pp][Nn][Gg])$/
                                });
                        });




