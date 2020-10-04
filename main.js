/* Components */

Vue.component('template-card-weather', {
    props : ['weather'],
    template : `
                <div class="c-card-w d-fx f-column f-center ">
                    <div><p><span class="color-white-light">Monday</span></p></div>
                    <div class="d-fx f-center"><img src="https://www.metaweather.com/static/img/weather/{{ weather.img_abbr }}.svg" alt="icon" class="c-card-img-weat"></div><!--Img card-->
                    <div><p><span class="c-temper-max color-white-light">{{ weather.max_temp }}</span> <span class="c-temper-min">{{ weather }}</span></p></div><!--Text-->
                </div><!--Card weather-->
                `
});


const weatherContent = new Vue({
    el : '#app-weather',
    data : {
        show : false,
        showDeegrees : true,
        searchLocation : '',
        cDeegreeSelect : 'c-deegree-select',
        nextDays : [],
        weatherToday : {},
        routeImage : `https://www.metaweather.com/static/img/weather/`,
        flagCelsius : false,
        unitMeasure : 'C'
    },
    beforeCreate : function(){
        console.log('antes de crearse');
        const este = this;
        const req = new XMLHttpRequest();
        req.open('GET', 'main.php?location=mexico', true); 
        
        req.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                const data = JSON.parse(this.responseText);
                console.log(data);
                este.nextDays = data['next_days'];
                console.log(este.nextDays);
                este.weatherToday = data['today'];
            }
        };
        req.send(null);
    },
    methods: {
        searchWeatherBTN : function(event){
            if(!this.searchLocation) return;
            const este = this;
            const req = new XMLHttpRequest();
            req.open('GET', `main.php?location=${this.searchLocation}`, true); 
            
            req.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    const data = JSON.parse(this.responseText);
                    este.nextDays = data['next_days'];
                    este.weatherToday = data['today'];
                }
            };
            req.send(null);
            this.searchLocation = '';
            if(this.flagCelsius){
                this.showDeegrees = !this.showDeegrees;
                this.flagCelsius = !this.flagCelsius;
                this.unitMeasure = '';
            }
        },
        src : function(routeImage){
            return `https://www.metaweather.com/static/img/weather/${routeImage}.svg`
        },
        celsius : function(){
            if(this.flagCelsius){
                this.unitMeasure = 'C';
                this.showDeegrees = !this.showDeegrees;
                this.weatherToday.the_temp = Math.round(((this.weatherToday.the_temp-32)*(5/9)));
                for (const index in this.nextDays) {
                    this.nextDays[index]['min_temp'] = Math.round(((this.nextDays[index]['min_temp']-32)*(5/9)));
                    this.nextDays[index]['max_temp'] = Math.round(((this.nextDays[index]['max_temp']-32)*(5/9)));
                }
                this.flagCelsius = false;
            }
            
        },
        fahrenheit : function(){
            if(!this.flagCelsius){
                this.unitMeasure = 'F';
                this.showDeegrees = !this.showDeegrees;
                this.weatherToday.the_temp = Math.round((this.weatherToday.the_temp*(9/5)))+32;
                for (const index in this.nextDays){
                    this.nextDays[index]['min_temp'] = Math.round((this.nextDays[index]['min_temp']*(9/5)))+32;
                    this.nextDays[index]['max_temp'] = Math.round((this.nextDays[index]['max_temp']*(9/5)))+32;
                }
                this.flagCelsius = true;
            }
        }
    }
});