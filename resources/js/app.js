var artyom = new Artyom();

(function () {
    console.log('Init smart');
    
    //SYSTEM OBJECT START//
    System = {
        moduleConfig: function (moduleName) {
            let config = null;
            
            $.getJSON({'url': '/modules/' + moduleName + '/config.json', 'async': false}, function (data) {
                config = data;
            });
            
            return config;
        },
        log: function (message, priority = 'info') {
            switch (priority) {
                case "info":
                    console.info(message);
                    break;
                case "error":
                    console.error(message);
                    break;
            }
        },
        updateInfo: function () {
            return false;
        }
    };
    //SYSTEM OBJECT END//
    
    var systemCommands = [{
        indexes: ["Gibt es Module Updates", "Gibt es irgendwelche Updates"],
        action: function () {
            let updateAvailable = System.updateInfo();
            
            if(updateAvailable) {
                artyom.say("Es gibt neue Updates für dich!");
            }else {
                artyom.say("Momentan gibt es keine neuen updates für dich!");
            }
        }
    }, {
        indexes: ["Erzähl mir einen Witz", "Guten morgen"],
        action: function () {
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