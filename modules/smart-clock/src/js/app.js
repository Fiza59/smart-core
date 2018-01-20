(function ($) {
    'use strict';
    console.log('Init smart-clock');

    updateWidget();

    setInterval(function () {
        updateWidget();
    }, 1000);

    function updateWidget() {
        var dateTime = new Date();

        var time = dateTime.getHours() + ':' + dateTime.getMinutes() + ':' + dateTime.getSeconds();
        var date =  dateTime.getDate() + '.' + (dateTime.getMonth()+1) + '.' + dateTime.getFullYear();

        $('.smart-clock .current-time').html(time);
        $('.smart-clock .current-date').html(date);
    }
})(jQuery);