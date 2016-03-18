var CompanyDetail = new Object();
CompanyDetail.ajaxLoader = '<div class="index-table-loading"><img class="ajax-loader" src="../images/ajax-loader.gif" /></div>';

CompanyDetail.updatePrice = function(){
    $.ajax({
        type:'GET',
        url: '../data/sina',
        data: {
            "ticker":Global.ticker,
            "house":Global.house,
        },
        success: function(result) {
            var priceObj = eval('('+result+')');
            $('#price_now').fadeOut(200).fadeIn(200).fadeOut(200).html(priceObj.price_now).fadeIn(200);
            $('#price_diff').fadeOut(200).fadeIn(200).fadeOut(200).html(priceObj.price_diff).fadeIn(200);
            $('#price_rate').fadeOut(200).fadeIn(200).fadeOut(200).html(priceObj.price_rate).fadeIn(200);
            $('#volumn_now').fadeOut(200).fadeIn(200).fadeOut(200).html(priceObj.volumn).fadeIn(200);
            $('#count_now').fadeOut(200).fadeIn(200).fadeOut(200).html(priceObj.count).fadeIn(200);
            if(priceObj.price_diff != '停牌') {
                if(priceObj.isP) {
                    $('.company-overview-price').removeClass('green').addClass('red');
                }
                else {
                    $('.company-overview-price').removeClass('red').addClass('green');
                }
            }
        }
    });
}

CompanyDetail.loadGraph = function(graphList) {
    $("#chart_body_section").html("");
    for(var gIndex in graphList){
        var chartDom = '<div class="company-chart-body" id="chart_body_'+gIndex+'"></div>';
        $("#chart_body_section").append(chartDom);
        $.ajax({
            type:"POST",
            url: "../graph/"+graphList[gIndex],
            data: {
                "divId":"chart_body_"+gIndex,
            },
            success: function(result) {
                eval(result);
            },
        });
    }
}

$(document).ready(function(){
    $("#menu_title_company").addClass('actived');

    $(".company-nav-body > span").click(function(){
        $(".company-nav-body > span").removeClass('actived');
        $(this).addClass('actived');
    });
    for(var sectionIndex in Global.graphLayout){
        (function(sectionIndex){
            $('#chart_nav_'+sectionIndex).click(function(){
                CompanyDetail.loadGraph(Global.graphLayout[sectionIndex]);
            });
        })(sectionIndex);
    }
    CompanyDetail.updatePrice();
    CompanyDetail.loadGraph(Global.graphLayout[0]);
    setInterval("CompanyDetail.updatePrice()",10000);
});
