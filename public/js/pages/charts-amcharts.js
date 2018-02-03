var scansPerGate = function (building, date) {
    var chart = AmCharts.makeChart("scansPerGateChart", {
        "type": "serial",
        "theme": "light",
        "addClassNames": true,
        "path": "js/global/plugins/amcharts/amcharts/images/",
        "autoMargins": false,
        "marginLeft": 30,
        "marginRight": 8,
        "marginTop": 10,
        "marginBottom": 26,
        "titles": [{
            "text": "Number of Scans per Gate in " + date
        }, {
            "text": "In Building.",
            "bold": false
        }],
        "balloon": {
            "adjustBorderColor": false,
            "horizontalPadding": 10,
            "verticalPadding": 8,
            "color": "#ffffff"
        },
        "color": '#888',
        "dataLoader": {
            "url": "http://localhost:8000/api/getScansPerGate/" + building + "/" + date,
            "format": "json"
        },
        "valueAxes": [{
            "position": "left",
            "axisAlpha": 0,
            "autoGridCount": false
        }],
        "startDuration": 1,
        "graphs": [{
            "alphaField": "alpha",
            "balloonText": "<span style='font-size:12px;'>[[title]] in [[category]]:<br><span style='font-size: 20px;'>[[value]]</span> [[additional]]</span>",
            "fillAlphas": 1,
            "type": "column",
            "valueField": "scans",
            "dashLengthField": "dashLengthColumn"
        }],
        "categoryField": "gate",
        "categoryAxis": {
            "gridPosition": "start",
            "axisAlpha": 0,
            "tickLength": 0
        },
        "export": {
            "enabled": true
        }
    });

    $('#scansPerGateChart').closest('.portlet').find('.fullscreen').click(function () {
        chart.invalidateSize();
    });
};


var scansPerDayPerMonth = function (building, month, year) {
    var chart = AmCharts.makeChart("scansPerDayPerMonthChart", {
        "type": "serial",
        "theme": "light",
        "color":    '#888888',
        "pathToImages": "js/global/plugins/amcharts/amcharts/images/",
        "titles": [{
           "text": "Number of scans per day for month " + month + " in " + year + "."
        }, {
            "text": "In Building.",
            "bold": false
        }],
        "dataLoader": {
            "url": "http://localhost:8000/api/getScansPerDayPerMonth/" + building + "/" + month + "/" + year,
            "format": "json",
            "postProcess": function (data, options) {
                if (data.length === 0) {
                    options.chart.addLabel(0, '50%', 'The chart contains no data', 'center');
                    options.chart.chartDiv.style.opacity = 1;
                    options.chart.validateNow();
                }
                return data;
            }
        },
        "balloon": {
            "cornerRadius": 6
        },
        "valueAxes": [{
            "position": "left",
            "axisAlpha": 0
        }],
        "graphs": [{
            "bullet": "square",
            "bulletBorderAlpha": 1,
            "bulletBorderThickness": 1,
            "fillAlphas": 0.3,
            "fillColorsField": "lineColor",
            "legendValueText": "[[value]]",
            "lineColorField": "lineColor",
            "valueField": "scans"
        }],
        "chartScrollbar": {},
        "chartCursor": {
            "categoryBalloonDateFormat": "YYYY MMM DD",
            "cursorAlpha": 0,
            "zoomable": false
        },
        "dataDateFormat": "YYYY-MM-DD",
        "categoryField": "date",
        "categoryAxis": {
            "dateFormats": [{
                "period": "DD",
                "format": "DD"
            }, {
                "period": "WW",
                "format": "MMM DD"
            }, {
                "period": "MM",
                "format": "MMM"
            }, {
                "period": "YYYY",
                "format": "YYYY"
            }],
            "parseDates": true,
            "autoGridCount": false,
            "axisColor": "#555555",
            "gridAlpha": 0,
            "gridCount": 50
        },
        "export": {
            "enabled": true
        }
    });

    $('#scansPerDayPerMonthChart').closest('.portlet').find('.fullscreen').click(function () {
        chart.invalidateSize();
    });
};


jQuery(document).ready(function () {
    var dateChart1 = $('#gateDate').val();
    var dateChart2 = $('#monthDate').val();
    var dateChart2Array = dateChart2.split("-");
    var buildingId = $('#gateBuildings').val();

    scansPerGate(buildingId, dateChart1);
    scansPerDayPerMonth(buildingId, dateChart2Array[1], dateChart2Array[0]);

    $('#gateDoneButton').on('click', function () {
        var date = $('#gateDate').val();
        var buildingId = $('#gateBuildings').val();
        scansPerGate(buildingId, date);
    });

    $('#monthDoneButton').on('click', function () {
        var dateChart2 = $('#monthDate').val().split("-");
        var buildingId = $('#gateBuildingsMonth').val();
        scansPerDayPerMonth(buildingId, dateChart2[1], dateChart2[0]);
    });
});