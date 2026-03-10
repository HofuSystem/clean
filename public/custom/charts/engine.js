$(document).ready(function () {

    let engine = new Engine
    engine.buildCharts()
    $('.charts-report .optimize-form').submit(function (e) {
        e.preventDefault();
        let reportId = $(this).parents('.charts-report').attr('id');
        if (ChartEndPoint.length) {
            let isMultiReport = $(this).find('#toggleSwitch').is(':checked');
            let filters = {};
            let versions = [];
            if (isMultiReport) {
                $(`#${reportId} .versions`).each(function (index, element) {
                    filters = {};
                    filters.from = $(this).find('.version-from').val().trim();
                    filters.to = $(this).find('.version-to').val().trim();
                    versions.push(filters);
                });
            } else {
                filters.from = $(this).find('#singleFromDate').val();
                filters.to = $(this).find('#singleToDate').val();
            }
            let charts = engine.getAllChartsFormHtml(reportId);
            let data = {};
            data.charts = charts;
            data.filters = filters;
            data.versions = versions;
            $.ajax({
                type: "POST",
                url: ChartEndPoint,
                data: data,
                dataType: "json",
                success: function (response) {
                    if (response.status) {
                        toastr.success(response.message);
                        engine.removeCharts(reportId)
                        for (const key in response.charts) {
                            let chartData = response.charts[key];

                            if (Array.isArray(chartData)) {
                                single = chartData[0];
                                engine.buildChart(single.type, chartData, reportId);
                            } else {
                                engine.buildChart(chartData.type, chartData, reportId);
                            }
                        }
                    } else {
                        toastr.error(response.message);
                    }
                }
            });

        }



    });

});
class Engine {
    constructor() {

    }

    // Method to build a chart
    getAllChartsFormHtml(reportId = null) {
        let charts = [];
        if (reportId) {
            reportId = '#' + reportId;
        } else {
            reportId = '';
        }
        $(reportId + ' .charts-system').each(function (index, element) {
            let key = $(this).attr('id');
            charts.push(key);
        });
        return charts;
    }

    // Method to build a chart
    buildCharts(reportId = null) {
        let charts = this.getAllChartsFormHtml();
        if (!charts.length) {
            return;
        }
        const self = this;
        let data = {};
        data.charts = charts;
        $.ajax({
            type: "POST",
            url: chartsLinks.load,
            data: data,
            dataType: "json",
            success: function (response) {

                if (response.status) {
                    toastr.success(response.message);
                    for (const key in response.charts) {
                        self.buildChart(response.charts[key].type, response.charts[key], reportId);
                    }
                } else {
                    toastr.error(response.message);
                }
            }
        });

        //here
    }

    // Method to add a chart to the engine
    buildChart(type, data, reportId = null) {
        if (type == 'charts-statistics') {
            let chart = new Statistics
            chart.create(data, reportId);
        } else if (type == 'charts-list') {
            let chartList = new ListChart
            chartList.create(data, reportId);
        } else if (type == 'charts-table') {
            let chartTable = new TableChart
            chartTable.create(data, reportId);
        } else if (type == 'charts-graph') {
            let chartGraph = new Graph
            chartGraph.create(data, reportId);
        }
    }

    // Method to remove a chart from the engine
    removeCharts(reportId = null) {
        if (reportId) {
            reportId = '#' + reportId;
        } else {
            reportId = '';
        }
        $(reportId + ' .charts-system').each(function (index, element) {
            //make the innter html empty
            $(this).html('');
            $(this).removeClass(function (index, className) {
                return (className.match(/(^|\s)col-\S+/g) || []).join(' ');
            });
        });


    }


}
class TableChart {
    create(chart, reportId = null) {

        if (Array.isArray(chart)) {
            chart.forEach(c => this.create(c, reportId));
            return;
        }

        if (reportId) {
            reportId = '#' + reportId;
        } else {
            reportId = '';
        }

        var div = $(reportId + ' #' + chart.slug).addClass(chart.classes);
        div.hide();
        let title = `${chart.title}`;
        let tableId = chart.slug + '-datatable';
        if (chart.from) {
            title += ` "${chart.from}`;
            tableId += chart.from;
        }
        if (chart.to) {
            title += ` - ${chart.to}"`;
            tableId += chart.to;

        }
        var createUrlBtn = '';
        if(chart.data.create_url){
            createUrlBtn = `<a href"${chart.data.create_url}" class="mt-1 btn btn-primary">+</a>`;
        }
        var cardHtml =
            '<div class="card">' +
            '<div class="card-header">' +
            '<div class="mb-3">' +
            '<h5 class="card-title mb-0">' + title + '</h5>' +
            createUrlBtn +
            '</div>' +
            '</div>' +
            '<div class="card-body ">' +
            '<table class="table w-100" id="' + tableId + '">' +
            '<thead class="text-center">' +
            '</thead>' +
            '<tbody>' +
            '</tbody>' +
            '</table>' +
            '</div>' +
            '</div>';

        div.append(cardHtml);
        div.find(`#${tableId} thead`).append(this.createThead(chart.cols));
        div.find('a').attr('href', chart.data.create_url);
        if (chart.data.create_url) {
            let datatable = $('#' + tableId).DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    type: 'POST',
                    url: chart.data.ajax_url,
                    data: function (d) {
                        d['cols'] = chart.cols;
                    },
                    headers: {},
                },
                order: [[1, "DESC"]],
                columns: chart.cols
            });
    
            datatable.on("draw", function () {
                $(".btn-destroy").click(function (ev) {
                    ev.preventDefault();
                    var target = $(this).attr("href");
                    $("#delete-recored").modal("show");
                    $("#delete-recored .modal-body form").attr("action", target);
                });
    
                $("#delete-recored form").on("submit", function (e) {
                    e.preventDefault();
                    $.ajax({
                        url: this.action,
                        method: "POST",
                        data: "_method=DELETE&_token=" + $('meta[name="csrf-token"]').attr("content"),
                        success: function () {
                            toastr.success("Item has been deleted successfully");
                            $("#delete-recored").modal("hide");
                            datatable.draw();
                        },
                        error: function () {
                            toastr.error("Failed to delete this item");
                        }
                    });
                });
            });
        }else{
            $.ajax({
                url: chart.data.ajax_url, // Laravel route that returns JSON data
                type: "POST",
                dataType: "json",
                success: function(response) {
                    let datatable = $('#' + tableId).DataTable({
                        data: response.data, // Use preloaded data
                        columns: chart.cols,
                      
                    });
                    datatable.on("draw", function () {
                        $(".btn-destroy").click(function (ev) {
                            ev.preventDefault();
                            var target = $(this).attr("href");
                            $("#delete-recored").modal("show");
                            $("#delete-recored .modal-body form").attr("action", target);
                        });
            
                        $("#delete-recored form").on("submit", function (e) {
                            e.preventDefault();
                            $.ajax({
                                url: this.action,
                                method: "POST",
                                data: "_method=DELETE&_token=" + $('meta[name="csrf-token"]').attr("content"),
                                success: function () {
                                    toastr.success("Item has been deleted successfully");
                                    $("#delete-recored").modal("hide");
                                    datatable.draw();
                                },
                                error: function () {
                                    toastr.error("Failed to delete this item");
                                }
                            });
                        });
                    });
                }
            });
        }

        div.show(1000);
    }

    createThead(cols) {
        let tr = $('<tr class="text-center">');
        cols.forEach(element => {
            tr.append(`<th class="text-center">${element.name}</th>`);
        });
        return tr;
    }
}

class ListChart {
    create(chart, reportId = null) {
        if (Array.isArray(chart)) {
            chart.forEach(c => this.create(c, reportId));
            return;
        }

        if (reportId) {
            reportId = '#' + reportId;
        } else {
            reportId = '';
        }

        var $div = $(reportId + ' #' + chart.slug).addClass(chart.classes);
        $div.hide();
        var $card = $('<div>').addClass('card mb-1').appendTo($div);
        var $cardHeader = $('<div>').addClass('card-header d-flex justify-content-between').appendTo($card);
        var $cardTitleContainer = $('<div>').addClass('card-title m-0 me-2').appendTo($cardHeader);
        let title = `${chart.title}`;
        if (chart.from) {
            title += ` "${chart.from}`;
        }
        if (chart.to) {
            title += ` - ${chart.to}"`;
        }
        $('<h5>').addClass('m-0 me-2').html(title).appendTo($cardTitleContainer);
        $('<small>').addClass('text-muted').html(chart.small_title).appendTo($cardTitleContainer);

        var $cardBody = $('<div>').addClass('card-body').appendTo($card);
        var $ul = $('<ul>').addClass('p-0 m-0').appendTo($cardBody);

        chart.data.forEach(function (item) {
            var $li = $('<li>').addClass('rec d-flex mb-1 pb-1').appendTo($ul);
            var $imageDiv = $('<div>').addClass('me-3').appendTo($li);
            $(item.image).addClass('rounded').width('46').appendTo($imageDiv);
            var $infoDiv = $('<div>').addClass('d-flex w-100 flex-wrap align-items-center justify-content-between gap-2').appendTo($li);
            var $detailsDiv = $('<div>').addClass('me-2').appendTo($infoDiv);
            $('<h6>').addClass('mb-0').html(item.title).appendTo($detailsDiv);
            $('<small>').addClass('text-muted d-block').html(item.under_title).appendTo($detailsDiv);
            var $progressDiv = $('<div>').addClass('user-progress d-flex align-items-center gap-1').appendTo($infoDiv);
            $('<p>').addClass('mb-0 fw-medium').html(item.side_title).appendTo($progressDiv);
        });
        $div.show(1000);
    }
}

class Statistics {
    create(chart, reportId = null) {
        if (Array.isArray(chart)) {
            chart.forEach(c => this.create(c, reportId));
            return;
        }

        if (reportId) {
            reportId = '#' + reportId;
        } else {
            reportId = '';
        }

        var cardDiv = $(reportId + ' #' + chart.slug).addClass(chart.classes);
        cardDiv.hide();
        let title = `${chart.title}`;
        if (chart.from) {
            title += ` "${chart.from}`;
        }
        if (chart.to) {
            title += ` - ${chart.to}"`;
        }
        var cardInnerHtml = '<div class="card mb-2">' +
            '<div class="card-header">' +
            '<div class="mb-3">' +
            '<h5 class="card-title mb-0">' + title + '</h5>' +
            '<small class="text-muted">' + chart.small_title + '</small>' +
            '</div>' +
            '</div>' +
            '<div class="card-body">' +
            '<div class="row gy-3">' +
            '</div>' +
            '</div>' +
            '</div>';

        cardDiv.append(cardInnerHtml);
        const self      = this;
        var statsRow    = cardDiv.find('.row').last();;
        var length      = chart.data.length;
        var classSize = 3;
        if(length == 1){
            classSize = 12;
        }else if(length == 2){
            classSize = 6;
        }else if(length == 3){
            classSize = 4;
        }
        $.each(chart.data, function (index, stat) {
            var statHtml = self.generateStatsHTML(stat, classSize);
            statsRow.append(statHtml);
        });

        cardDiv.show(1000);
        return cardDiv;
    }

    generateStatsHTML(stat, classSize) {
        var statDiv = $('<div>').addClass(`col-md-${classSize} col-6`);
        var innerHtml = '<div class="d-flex align-items-center">' +
            '<div class="badge rounded-pill bg-label-primary me-3 p-2"><i style="color: ' + stat.color + ' !important;" class="' + stat.icon + '"></i></div>' +
            '<div class="card-info">' +
            '<h5 class="mb-0">' + stat.count + '</h5>' +
            '<small>' + stat.title + '</small>' +
            '</div>' +
            '</div>';
        statDiv.html(innerHtml);
        return statDiv;
    }
}
class Graph {
    create(chart, reportId = null) {
        
        if (Array.isArray(chart)) {
            chart.forEach(c => this.create(c, reportId));
            return;
        }

        if (reportId) {
            reportId = '#' + reportId;
        } else {
            reportId = '';
        }

        var cardDiv = $(reportId + ' #' + chart.slug).addClass(chart.classes);
        cardDiv.hide();
        let title = `${chart.title}`;
        let chartId = chart.slug + '-chart';
        console.log(chart);
        
        if (chart.from) {
            title += ` "${chart.from}`;
            chartId += chart.from;
        }
        if (chart.to) {
            title += ` - ${chart.to}"`;
            chartId += chart.to;
        }
        var cardInnerHtml = `
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <h5 class="card-title mb-0"><i class="${chart.data.icon}"></i> ${title}</h5>
                        <small class="text-muted">${chart.small_title}</small>
                    </div>
                    <div class="d-sm-flex d-none align-items-center">

                    </div>
                </div>
                <div class="card-body">
                    <div id="${chartId}"></div>
                </div>
            </div>
        `;

        cardDiv.append(cardInnerHtml);
        let cardColor, headingColor, labelColor, borderColor, legendColor;

        if (isDarkStyle) {
            cardColor       = config.colors_dark.cardColor;
            headingColor    = config.colors_dark.headingColor;
            labelColor      = config.colors_dark.textMuted;
            legendColor     = config.colors_dark.bodyColor;
            borderColor     = config.colors_dark.borderColor;
        } else {
            cardColor       = config.colors.cardColor;
            headingColor    = config.colors.headingColor;
            labelColor      = config.colors.textMuted;
            legendColor     = config.colors.bodyColor;
            borderColor     = config.colors.borderColor;
        }
    
        // Color constant
        const chartColors = {
            column: {
                series1 : chart.data.color,
                series2 : chart.data.color,
                bg      : chart.data.color
            },
            donut: {
                series1: chart.data.color,
                series2: chart.data.color,
                series3: chart.data.color,
                series4: chart.data.color
            },
            area: {
                series1: chart.data.color,
                series2: chart.data.color,
                series3: chart.data.color
            }
        };
        
        const lineChartEl = document.querySelector(`#${chartId}`),
            lineChartConfig = {
                chart: {
                    height: 300,
                    type: 'line',
                    parentHeightOffset: 0,
                    zoom: {
                        enabled: false
                    },
                    toolbar: {
                        show: false
                    }
                },
                series: [
                    {
                        data: chart.yaxis
                    }
                ],
                markers: {
                    strokeWidth: 7,
                    strokeOpacity: 1,
                    strokeColors: chart.data.color,
                    colors: [chart.data.color]
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'straight'
                },
                colors: [chart.data.color],
                grid: {
                    borderColor: borderColor,
                    xaxis: {
                        lines: {
                            show: true
                        }
                    },
                    padding: {
                        top: -20
                    }
                },
                tooltip: {
                    custom: function ({
                        series,
                        seriesIndex,
                        dataPointIndex,
                        w
                    }) {
                        return '<div class="px-3 py-2">' + '<span>' + series[seriesIndex][dataPointIndex] + " "+chart.data.title+' </span>' +'</div>';
                    }
                },
                xaxis: {
                    categories: chart.xaxis,
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: labelColor,
                            fontSize: '13px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: labelColor,
                            fontSize: '13px'
                        }
                    }
                }
            };
        if (typeof lineChartEl !== undefined && lineChartEl !== null) {
            const lineChart = new ApexCharts(lineChartEl, lineChartConfig);
            lineChart.render();
        }

        cardDiv.show(1000);
        return cardDiv;
    }


}

