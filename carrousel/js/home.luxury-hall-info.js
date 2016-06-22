/* 
 * Author: Chees
 * Description: Home > Luxury hall info 愿�� 湲곕낯 js
 */

$(function(){
    if ( $('body').hasClass('home') == false ) return false;
    setDateStr();
    getWeatherData();
});

var setDateStr = function() {
    var date = new Date();
    var dayStr = date.toString().substr(0,3);
    var day = date.getDate();
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    month = (parseInt(month) < 10 ) ? '0' + month : month;
    day = (parseInt(day) < 10 ) ? '0' + day : day ;
    var dateStr = year + '.' + month + '.' + day + ' ' + dayStr;

    var hour = date.getHours();
    var minute = date.getMinutes();
    hour = (parseInt(hour) < 10 ) ? '0' + hour : hour;
    minute = (parseInt(minute) < 10 ) ? '0' + minute : minute;
    $('.lhi-content .date').html(dateStr);
}

var getWeatherData = function() {
    var url = 'http://api.openweathermap.org/data/2.5/weather';
    $.support.cors = true;
    $.ajax({
        type: "GET",
        url: url,
        crossDomain: true,
        data: {q:'Seoul,kr'},
        cache: false,
        dataType: "jsonp",
        success: function(json) {
            parseWeatherData(json);
        },
        complete: function(e) {
            console.log(e);
        }
    });
};

var parseWeatherData = function(res) {


    //res = $.parseJSON(res);
    var temp = parseFloat(res.main.temp) - 273.15;
    temp = Math.round(temp);
    var weather = res.weather[0].icon;

    if ( temp < 0 ) {
        $('.temperature .minus').show();
    } else {
        $('.temperature .minus').hide();
    }

    var tempStr = Math.abs(temp);

    var n1 = Math.floor(tempStr/10);
    var n2 = tempStr%10;
    $('.temperature #n1').attr('class', 'digit-' + n1 ).html(n1);
    $('.temperature #n2').attr('class', 'digit-' + n2 ).html(n2);

    if ( parseInt(n1) == 0 ) $('.temperature #n1').hide();
    else $('.temperature #n1').show();
    var weatherType = 'weather-';
    var weatherAlt;
    switch (weather) {
        case '01d':
        case '01n':
            weatherType += 'sunny';
            weatherAlt = '留묒쓬�좎뵪';
            break;

        case '02d':
        case '02n':
            weatherType += 'clear';
            weatherAlt = '援щ쫫��좎뵪';
            break;

        case '03d':
        case '03n':
        case '04d':
        case '04n':
            weatherType += 'cloudy';
            weatherAlt = '援щ쫫�좎뵪';
            break;

        case '09d':
        case '09n':
        case '10d':
        case '10n':
        case '11d':
        case '11n':
            weatherType += 'rainy';
            weatherAlt = '鍮꾨궇��';
            break;

        case '13d':
        case '13n':
            weatherType += 'snowy';
            weatherAlt = '�덈궇��';
            break;

        case '50d':
        case '50n':
            weatherType += 'cloudy';
            weatherAlt = '援щ쫫�좎뵪';
            break;

    }

    var weatherIcon = $('<div class="anim-obj ' + weatherType + '" frames="10" fps="10" img-height="160"><div class="frame sprite" title='+weatherAlt+' style="text-indent:-9999em;">'+weatherAlt+'</div></div>');
    $('.weather .status').html(weatherIcon);

    startMotion($('.weather .status > .anim-obj'));
};


var setTimeToWatch = function(hour,minute) {

    //console.log(hour,minute);

    var hh1 = Math.floor(hour/10);
    var hh2 = hour%10;
    $('.digital-watch #hh1').attr('class', 'digit-' + hh1 ).html(hh1);
    $('.digital-watch #hh2').attr('class', 'digit-' + hh2 ).html(hh2);

    var mm1 = Math.floor(minute/10);
    var mm2 = minute%10;
    $('.digital-watch #mm1').attr('class', 'digit-' + mm1 ).html(mm1);
    $('.digital-watch #mm2').attr('class', 'digit-' + mm2 ).html(mm2);
}



// SVG ANALOG CLOCK
$(function() {

    if ( $('html').hasClass('svg') == false ) {
        $('.analog-watch').remove();
    }

    (function($){
        var units = [{name:"vps",op:"divide",by:1},
            {name:"bps",op:"divide",by:1},
            {name:"vpm",op:"divide",by:60},
            {name:"bpm",op:"divide",by:60},
            {name:"vph",op:"divide",by:60*60},
            {name:"bph",op:"divide",by:60*60},
            {name:"hz",op:"multiply",by:2}];
        $.each(units, function(idx, unit) {
            if(unit.op === "divide") {
                unit.multiplyFactor = 1/unit.by;
            } else if(unit.op === "multiply") {
                unit.multiplyFactor = unit.by;
            }
        });
        function TickGenerator(interval) {
            //default : second hand tick occurs 1 time per sec.
            //If 3Hz, or 3*2*3600=21600vPh(vibration per hour) is to be simulated,
            //tickInterval value should be calcuated to a value 1000/(3*2), which means
            //6 jumps per second.
            //for 4Hz, or 4*2*3600=28,800vPh, 1000/(4*2) = 125ms. 8 jumps per second.
            //for 2.5Hz, or 2.5*2*3600=18,800vPh, 1000/(2.5*2) = 200ms. 5 jumps per second.
            //480vpm or bpm -> 480/60=8 jumps per second
            this.jumpsPerSecond = 1;
            this.interval = "1vps";
            if(interval !== undefined && interval !== null) {
                this.setInterval(interval);
            }
            this.tickHandlers = $.Callbacks();
            this.tickTimer = null;
        }
        TickGenerator.I_YEAR = 0;
        TickGenerator.I_MONTH = 1;
        TickGenerator.I_DATE = 2;
        TickGenerator.I_DAY = 3;
        TickGenerator.I_HOUR = 4;
        TickGenerator.I_MINUTE = 5;
        TickGenerator.I_SECOND = 6;
        TickGenerator.I_MILLI = 7;
        TickGenerator.I_INST = 8;
        TickGenerator.prototype.setInterval = function(interval) {
            var self = this;
            if(interval === undefined || interval === null) {
                interval = this.interval;
            }
            if(typeof interval === "number") {
                interval = (interval + "vps");
            }
            $.each(units, function(idx,unit) {
                if(interval.toLowerCase().indexOf(unit.name.toLowerCase())>=0) {
                    var value = Number(interval.toLowerCase().split(unit.name.toLowerCase())[0])
                        * unit.multiplyFactor;
                    if(!isNaN(value)) {
                        self.jumpsPerSecond = value|0;//integer only.
                        self.interval = interval;
                        return false;
                    }
                }
            });
        };
        TickGenerator.prototype.start = function() {
            if(this.tickTimer) {
                return;
            }
            var self = this;
            self.tickTimer = {timer:null};
            function tick(date) {
                date = date || new Date();
                self.tick(date);
                self.setNextTickTimeout(date, tick);
            }
            tick(new Date());
        };
        TickGenerator.prototype.stop = function() {
            if(this.tickTimer) {
                clearTimeout(this.tickTimer.timer);
                this.tickTimer = null;
            }
        };
        TickGenerator.prototype.setNextTickTimeout = function(date, handler) {
            if(!date || !this.tickTimer) {
                return;
            }
            var current = new Date(date);
            var next = this.getCurrentTickTime(date,1);
            var diff = next[TickGenerator.I_INST].getTime() - current.getTime(); //difference in milliseconds.
            this.tickTimer.timer = setTimeout(handler, diff);
        };
        TickGenerator.prototype.addTickHandler = function(handler) {
            this.tickHandlers.add(handler);
        };
        TickGenerator.prototype.removeTickHandler = function(handler) {
            this.tickHandlers.remove(handler);
        };
        TickGenerator.prototype.getCurrentTickTime = function(date, phase) {
            date = date ? new Date(date) : new Date();
            date.setMilliseconds(date.getMilliseconds() +
                (typeof phase === "number" ? (1000*(phase/this.jumpsPerSecond)) : 0));
            //TODO round milliseconds according to jumpsPerSecond
            for(var i=0; i<(this.jumpsPerSecond); i++) {
                var a = 1000*(i/this.jumpsPerSecond);
                var b = 1000*((i+1)/this.jumpsPerSecond);
                var m = date.getMilliseconds();
                if(a <= m && m <= b) {
                    date.setMilliseconds(Math.abs(a-m) < Math.abs(b-m) ? a : b);
                }
            }
            var t = [];
            t[ TickGenerator.I_YEAR  ] = date.getFullYear();
            t[ TickGenerator.I_MONTH ] = date.getMonth() + 1;
            t[ TickGenerator.I_DATE  ] = date.getDate();
            t[ TickGenerator.I_DAY   ] = date.getDay();//0 sunday
            t[ TickGenerator.I_HOUR  ] = date.getHours();
            t[ TickGenerator.I_MINUTE] = date.getMinutes();
            t[ TickGenerator.I_SECOND] = date.getSeconds();
            t[ TickGenerator.I_MILLI ] = date.getMilliseconds();
            t[ TickGenerator.I_INST  ] = date;
            return t;
        };
        TickGenerator.prototype.tick = function(date) {
            //TODO Role of tick handlers?
            this.tickHandlers.fire.apply(this.tickHandlers, this.getCurrentTickTime(date));
        };
        window.TickGenerator = TickGenerator;
    })(jQuery);

    var interval = null;
    if(window.widget) {
        interval = widget.preferenceForKey('interval');
    }
    var ticker = new TickGenerator(interval);
    window.ticker = ticker;
    var hourhand = $('#hourhand');
    var minhand = $('#minutehand');
    var sechand = $('#secondhand');
    ticker.addTickHandler(function() {
        var args = $.makeArray(arguments).slice(0,TickGenerator.I_MILLI+1);
        var s = args[TickGenerator.I_SECOND] * 1000 + args[TickGenerator.I_MILLI];
        var m = args[TickGenerator.I_MINUTE] * 60 * 1000 + s;
        var h = (args[TickGenerator.I_HOUR]%12) * 3600 * 1000 + m
        hourhand.attr('transform', 'rotate('+(h*30/(3600*1000))+',50,50)');
        minhand.attr('transform',  'rotate('+(m*6/(60*1000))   +',50,50)');
        sechand.attr('transform',  'rotate('+(s*6/(1000))      +',50,50)');
        setTimeToWatch(args[TickGenerator.I_HOUR], args[TickGenerator.I_MINUTE]);
    });

    ticker.start();


});