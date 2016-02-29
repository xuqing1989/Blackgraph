$(document).ready(function(){
    $("#menu_title_company").addClass('actived');

    $(".company-nav-body > span").click(function(){
        $(".company-nav-body > span").removeClass('actived');
        $(this).addClass('actived');
    });

    $("#chart_nav_2").click(function(){
        $.ajax({
            type:'GET',
            url: '../api/fdmtis',
            data: {
                "ticker":Global.ticker,
                "endDate":moment().format('YYYYMMDD'),
            },
            success: function(apiData) {
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
            }
        });
    });
});
