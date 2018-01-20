(function () {
    console.log('Init smart');

    System = {
        moduleConfig: function (moduleName) {
            let config = null;

            $.getJSON({'url': '/modules/' + moduleName + '/config.json', 'async': false}, function (data) {
                config = data;
            });

            return config;
        },
        log: function (message, priority = 'info') {
            switch(priority) {
                case "info":
                    console.info(message);
                    break;
                case "error":
                    console.error(message);
                    break;
            }
        }
    };
})();