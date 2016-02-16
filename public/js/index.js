$(document).ready(function(){
    var cal = new CalHeatMap();
    cal.init({
        itemSelector: "#test2",
        domain: "month",
        subDomain: "x_day",
        data: "../data/calendar?startDate={{t:start}}&endDate={{t:end}}",
        dataType: "json",
        start: new Date(moment().year(), moment().month(), 0),
        cellSize: 30,
        cellPadding: 5,
        domainGutter: 30,
        range: 3,
        domainDynamicDimension: false,
        domainLabelFormat: function(date) {
            return moment(date).format("YYYY年MM月");
        },
        label: {
            position:'top',
        },
        previousSelector: "#cal_left",
        nextSelector: "#cal_right",
        subDomainTextFormat: "%d",
        subDomainTitleFormat: {
            empty:"无财报",
            filled:"{count}份财报披露",
        },
        legendTitleFormat: {
            lower: "当日财报披露数量少于{min}",
            inner: "当日财报披露数量在{down}到{up}份之间",
            upper: "当日财报披露数量多于{max}",
        },
        legend: [10, 30, 50, 100],
        highlight:["now"],
    });
    $("#cal_selector_"+moment().day()).addClass('actived');
    for(var i=1;i<=7;i++){
        $("#cal_selector_"+i+">.date").html(moment().day(i).format("MM-DD"));
    }
});
