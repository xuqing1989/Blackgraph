var indexObj = new Object();

indexObj.activeDate = new moment();
indexObj.cal = new CalHeatMap();
indexObj.filter = {'industry':'all',
                   'subindustry':'all',
                   'flag':'all',
                   'page':'all'};

indexObj.order = {'key':'market_cup','value':0};

indexObj.loadDate = function() {
    var theDate = indexObj.activeDate;
    indexObj.report_list(theDate.format('YYYY-MM-DD'),indexObj.filter);
    indexObj.cal.highlight(theDate.toDate());
    indexObj.cal_selector(theDate);

}

indexObj.report_list = function(report_date,filterObj){
    $('#ajax_report_list').html('');
    $('.ajax_loader').show();
    $.ajax({
        type:'GET',
        url: '../data/report',
        data: {
            "date":report_date,
            "industry":filterObj.industry,
            "subindustry":filterObj.subindustry,
            "flag":filterObj.flag,
            "page":filterObj.page,
            "order1":indexObj.order.key,
            "order2":indexObj.order.value,
        },
        success: function(result) {
            $('.ajax_loader').hide();
            $('#ajax_report_list').html(result);
            $('.company_table_order').click(function(){
                indexObj.order.key = $(this).attr('order');
                if(!$(this).attr('orderSelect')) {
                    indexObj.order.value = 1;
                }
                else {
                    if($(this).attr('orderP') == '1') {
                        indexObj.order.value = 0;
                    }
                    else {
                        indexObj.order.value = 1;
                    }
                }
                //indexObj.filter.page = 1;
                indexObj.report_list(report_date,filterObj);
            });
        }
    });
}

indexObj.cal_selector = function(theDate) {
    var selectDate = new moment(theDate);
    $("[id^=cal_selector_]").removeClass('actived');
    $("#cal_selector_"+selectDate.isoWeekday()).addClass('actived');
    for(var i=1;i<=7;i++){
        //in momentjs 0 represent SUN, 6 represent SAT
        $("#cal_selector_"+i+">.date").html(selectDate.isoWeekday(i).format("MM-DD"));
        $("#cal_selector_"+i+">.date").attr('full-date',selectDate.isoWeekday(i).format("YYYY-MM-DD"));
    }
}

$(document).ready(function(){
    $("#menu_title_cal").addClass('actived');
    indexObj.cal.init({
        itemSelector: "#test2",
        domain: "month",
        subDomain: "x_day",
        data: "../data/calendar?startDate={{t:start}}&endDate={{t:end}}&industry="+
                            indexObj.filter.industry+"&subindustry="+indexObj.filter.subindustry+
                            "&flag="+indexObj.filter.flag,
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
        onClick: function(clickDate,nb) {
            indexObj.activeDate = moment(clickDate);
            indexObj.loadDate();
        },
    });

    indexObj.loadDate();

    $(".industry_list_select").click(function(){
        var industryName = $(this).html();
        var sublist = $(this).attr('sub');
        sublist = eval('('+sublist+')');
        var sublistHTML = '<li class="subindustry_list_select" sid="all">全部</li>';
        for(var i=0;i<sublist.length;i++) {
            sublistHTML += '<li sid="'+sublist[i]['id']+'" class="subindustry_list_select">' + sublist[i]['name']+'</li>';
        }
        $('#subindustry_list').html(sublistHTML);
        $('.subindustry_list_select').click(function(){
            $("#subindustry_title").html($(this).html()+'&nbsp;&#9660;');
            $("#subindustry_dropdown").removeClass('is-open');
            indexObj.filter.subindustry=$(this).attr('sid');
            indexObj.report_list(indexObj.activeDate.format('YYYY-MM-DD'),indexObj.filter);
            indexObj.cal.update("../data/calendar?startDate={{t:start}}&endDate={{t:end}}&industry="+
                                indexObj.filter.industry+"&subindustry="+indexObj.filter.subindustry+
                                "&flag="+indexObj.filter.flag);
            indexObj.cal.options.data = "../data/calendar?startDate={{t:start}}&endDate={{t:end}}&industry="+
                                        indexObj.filter.industry+"&subindustry="+indexObj.filter.subindustry+
                                        "&flag="+indexObj.filter.flag;
        });
        $('#industry_dropdown').removeClass('is-open');
        $("#subindustry_title").html('全部&nbsp;&#9660;');
        $('#industry_title').html(industryName+'&nbsp;&#9660;');
        indexObj.filter.industry=$(this).attr('sid');
        indexObj.filter.subindustry='all';
        indexObj.loadDate();
        indexObj.cal.update("../data/calendar?startDate={{t:start}}&endDate={{t:end}}&industry="+
                            indexObj.filter.industry+"&subindustry="+indexObj.filter.subindustry+
                            "&flag="+indexObj.filter.flag);
        indexObj.cal.options.data = "../data/calendar?startDate={{t:start}}&endDate={{t:end}}&industry="+
                                    indexObj.filter.industry+"&subindustry="+indexObj.filter.subindustry+
                                    "&flag="+indexObj.filter.flag;
    });

    $("#industry_list_all").click(function(){
        $('#industry_dropdown').removeClass('is-open');
        $('#subindustry_dropdown').removeClass('is-open');
        $('#industry_title').html('全部&nbsp;&#9660;');
        $("#subindustry_title").html('全部&nbsp;&#9660;');
        $('#subindustry_list').html('<li class="subindustry_list_select" sid="all">全部</li>');
        $('.subindustry_list_select').click(function(){
            $("#subindustry_dropdown").removeClass('is-open');
        });
        indexObj.filter.industry='all';
        indexObj.filter.subindustry='all';
        indexObj.loadDate();
        indexObj.cal.update("../data/calendar?startDate={{t:start}}&endDate={{t:end}}&industry="+
                            indexObj.filter.industry+"&subindustry="+indexObj.filter.subindustry+
                            "&flag="+indexObj.filter.flag);
        indexObj.cal.options.data = "../data/calendar?startDate={{t:start}}&endDate={{t:end}}&industry="+
                                    indexObj.filter.industry+"&subindustry="+indexObj.filter.subindustry+
                                    "&flag="+indexObj.filter.flag;
    });

    $(".index-cal-selector-title").click(function(){
        indexObj.activeDate = moment($(this).children('.date').attr('full-date'));
        indexObj.loadDate();
    });

    $("#cal_selector_left").click(function(){
        indexObj.activeDate.subtract(1,'week').isoWeekday(1);
        indexObj.loadDate();
    });

    $("#cal_selector_right").click(function(){
        indexObj.activeDate.add(1,'week').isoWeekday(1);
        indexObj.loadDate();
    });

    $('#flag_checkbox').attr('checked',false);

    $('#flag_checkbox').click(function(){
        if($(this).is(':checked')){
            indexObj.filter.flag=1;
        }
        else {
            indexObj.filter.flag='all';
        }
        indexObj.loadDate();
        indexObj.cal.update("../data/calendar?startDate={{t:start}}&endDate={{t:end}}&industry="+
                            indexObj.filter.industry+"&subindustry="+indexObj.filter.subindustry+
                            "&flag="+indexObj.filter.flag);
        indexObj.cal.options.data = "../data/calendar?startDate={{t:start}}&endDate={{t:end}}&industry="+
                                    indexObj.filter.industry+"&subindustry="+indexObj.filter.subindustry+
                                    "&flag="+indexObj.filter.flag;
    });
});


