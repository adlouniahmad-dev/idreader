let scansPerGate = function (building, date, buildingName) {
    let chart = AmCharts.makeChart("scansPerGateChart", {
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
            "text": "In " + buildingName + " Building.",
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


let scansPerDayPerMonth = function (building, month, year, buildingName) {
    let chart = AmCharts.makeChart("scansPerDayPerMonthChart", {
        "type": "serial",
        "theme": "light",
        "color":    '#888888',
        "pathToImages": "js/global/plugins/amcharts/amcharts/images/",
        "titles": [{
           "text": "Number of scans per day for month " + month + " in " + year + "."
        }, {
            "text": "In " + buildingName + " Building.",
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
        "categoryField": "date_created",
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
    let dateChart1 = $('#gateDate').val();
    let dateChart2 = $('#monthDate').val();
    let dateChart2Array = dateChart2.split("-");
    let buildingId = $('#gateBuildings').val();
    let buildingName1 = $('#gateBuildings option:selected').text();
    let buildingName2 = $('#gateBuildingsMonth option:selected').text();

    scansPerGate(buildingId, dateChart1, buildingName1);
    scansPerDayPerMonth(buildingId, dateChart2Array[1], dateChart2Array[0], buildingName2);

    $('#gateDoneButton').on('click', function () {
        let date = $('#gateDate').val();
        let buildingId = $('#gateBuildings').val();
        let buildingName = $('#gateBuildings option:selected').text();
        scansPerGate(buildingId, date, buildingName);
    });

    $('#monthDoneButton').on('click', function () {
        let dateChart2 = $('#monthDate').val().split("-");
        let buildingId = $('#gateBuildingsMonth').val();
        scansPerDayPerMonth(buildingId, dateChart2[1], dateChart2[0]);
    });
});