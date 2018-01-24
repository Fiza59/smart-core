(function () {
    console.log('Init smart');
    
    var artyom = new Artyom();
    var commandHello = {
        indexes:["hallo","Guten morgen"], // These spoken words will trigger the execution of the command
        action:function(){ // Action to be executed when a index match with spoken word
            artyom.say("Hallo was kann ich für dich tun?");
        }
    };
    
    artyom.addCommands(commandHello); // Add the command with addCommands method. Now
    
    artyom.initialize({
        lang:"de-DE",// A lot of languages are supported. Read the docs !
        continuous:true,// recognize 1 command and stop listening !
        listen:false, // Start recognizing
        debug:true, // Show everything in the console
        obeyKeyword: 'Hör mir zu',
        speed:1 // talk normally
    }).then(function(){
        console.log("Ready to work !");
    });

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