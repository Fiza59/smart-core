(function ($) {
    'use strict';
    System.log('Init smart-weather');

    //first initialize
    collectWeatherData();

    //read config
    let config = System.moduleConfig('smart-weather');

    console.log(config);

    //600000 ms are 10 minutes
    setInterval(function () {
        collectWeatherData();
    }, 600000);

    function collectWeatherData() {
        $.simpleWeather({
            woeid: '667931',
            unit: 'c',
            success: function(weather) {
                $('.smart-weather .current-temperature').html(weather.temp + '&deg;' + weather.units.temp);
                $('.smart-weather .current-position').html(weather.city + ' ' + weather.region);

                let count = 0;
                weather.forecast.forEach(function (item) {
                    if(count < config.maxForecasts) {
                        let newItem = '<div class="forecast-item">'
                            + '<h1 class="date">' + item.date + '</h1>'
                            + '<div class="row center-y"><i class="zmdi zmdi-triangle-up"></i><div class="high"><span>' + item.high + '&deg;' + weather.units.temp + '</span></div>'
                            + '<div class="low"><i class="zmdi zmdi-triangle-down"></i><span>' + item.low + '&deg;' + weather.units.temp + '</span></div></div>'
                            + '</div>';
                        $('.smart-weather .forecast-container').append(newItem);

                        count++;
                    }
                })
            },
            error: function (res) {
                System.log('smart-weather: ' + res, 'error');
            }
        });
    }
})(jQuery);