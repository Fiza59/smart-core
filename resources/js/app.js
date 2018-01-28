var artyom = new Artyom(),
    timeManagerList = [],
    timeManagerTimer = null,
    timeManagerTick = 0;

(function () {
    //SYSTEM OBJECT START//
    System = {
        moduleConfig: function (moduleName) {
            let config = null;

            $.getJSON({'url': '/modules/' + moduleName + '/config.json', async: false}, function (data) {
                config = data;
            });

            return config;
        },
        log: function (message, priority = 'info') {
            switch (priority) {
                case "info":
                    console.info('%cSmart info:' + '%c ' + message, 'color: white;background: blue; padding:2px;', 'color: black');
                    break;
                case "error":
                    console.info('%cSmart error:' + '%c ' + message, 'color: white;background: red; padding:2px;', 'color: black');
                    break;
            }
        },
        updateInfo: function () {
            return false;
        },
        saySomething: function (text) {
            artyom.say(text);
        },
        openActionField: function () {
            $('.action-container').fadeIn();
        },
        closeActionField: function () {
            $('.action-container').fadeOut();
        },
        request: function (type, url, data = null, callback) {
            $.ajax({
                type: type,
                url: url,
                data: data,
                success: function (response) {
                    callback(response);
                },
                error: function (response) {
                    System.log(JSON.stringify(response), 'error');
                }
            })
        },
        addToTimeEvents: function (seconds, callback) {
            timeManagerList.push({
                time: seconds,
                callback: callback
            })
        },
        initTimer: function () {
            timeManagerTimer = setInterval(function () {
                System.callTimeManager();
            }, 1000);
        },
        callTimeManager: function () {
            timeManagerList.forEach(function (item) {
                if (timeManagerTick % item.time === 0) {
                    item.callback();
                }
            });

            timeManagerTick++;
        }
    };
    //SYSTEM OBJECT END//

    System.log('Init smart');
    System.initTimer();

    const systemCommands = [{
        indexes: [
            "gibt es Modul updates",
            "gibt es Module updates",
            "Gibt es irgendwelche Updates"
        ], action: function () {
            let updateAvailable = System.updateInfo();

            if (updateAvailable) {
                artyom.say("Es gibt neue Updates für dich!");
            } else {
                artyom.say("Momentan gibt es keine neuen updates für dich!");
            }
        }
    }, {
        indexes: [
            "Erzähl mir einen Witz",
            "Guten morgen"
        ], action: function () {
            artyom.say("Hallo was kann ich für dich tun?");
        }
    }];

    artyom.addCommands(systemCommands); // Add the command with addCommands method. Now

    artyom.initialize({
        lang: "de-DE",
        continuous: false,
        listen: false,
        debug: true,
        //obeyKeyword: 'Smart',
        speed: 1
    }).then(function () {
        console.log("Ready to work !");
    });
})();