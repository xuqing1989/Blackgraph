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

$(document).ready(function(){
    $("#menu_title_company").addClass('actived');

    $(".company-nav-body > span").click(function(){
        $(".company-nav-body > span").removeClass('actived');
        $(this).addClass('actived');
    });

    $("#chart_nav_2").click(function(){
        $("[id^=chart_body_]").html(CompanyDetail.ajaxLoader);
        $.ajax({
            type:'GET',
            url: '../api/fdmtis',
            data: {
                "ticker":Global.ticker,
                "endDate":moment().format('YYYYMMDD'),
            },
            success: function(apiData) {
                Global.fdmtis = apiData;
                $.ajax({
                    type:'POST',
                    url: '../graph/profitmargin',
                    data: {
                        "divId":'chart_body_1',
                        "apiData":apiData,
                    },
                    success: function(result) {
                        eval(result);
                    }
                });
                if(Global.fdmtbs) {
                }
            }
        });
        $.ajax({
            type:'GET',
            url: '../api/fdmtbs',
            data: {
                "ticker":Global.ticker,
                "endDate":moment().format('YYYYMMDD'),
            },
            success: function(apiData) {
                Global.fdmtbs = apiData;
                if(Global.fdmtis) {
                }
                $.ajax({
                    type:'POST',
                    url: '../graph/liquidity',
                    data: {
                        "divId":'chart_body_3',
                        "apiData":apiData,
                    },
                    success: function(result) {
                        eval(result);
                    }
                });
                $.ajax({
                    type:'POST',
                    url: '../graph/assetsliabilityrate',
                    data: {
                        "divId":'chart_body_4',
                        "apiData":apiData,
                    },
                    success: function(result) {
                        eval(result);
                    }
                });
            }
        });
    });
    CompanyDetail.updatePrice();
    setInterval("CompanyDetail.updatePrice()",10000);
});
