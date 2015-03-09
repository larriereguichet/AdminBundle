var Backoffice = {
    init: function () {
        // alert on item deletion
        //$('.item-delete').on('click', function () {
        //    return confirm('It will be deleted!!! Are you really really sure ? 100% ?');
        //});
        // init table sort
        $('.table-sort').stupidtable();
    }
};

var HighChartHelper = {
    init: function (container, options) {
        container.highcharts({
            chart: {
                type: options.type,
                backgroundColor: '#303030'
            },
            title: {
                text: options.title
            },
            xAxis: {
                categories: ['Apples', 'Bananas', 'Oranges']
            },
            yAxis: {
                title: {
                    text: 'Fruit eaten'
                }
            },
            series: options.series
        });
    }
};

$(document).on('ready', function () {
    Backoffice.init();
});