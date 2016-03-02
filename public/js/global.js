Global = new Object();

$(document).ready(function(){
    $("#search_company").bind('input propertychange',function(){
        if($(this).val()) {
            $("#search_dropdown").addClass('is-open');
            $.ajax({
                type:'GET',
                url: '../data/search',
                data: {
                    "text":$(this).val(),
                },
                success: function(result) {
                    if($("#search_company").val()) {
                        var searchResult = eval('('+result+')');
                        var liHtml = "";
                        for(var key in searchResult){
                            liHtml += '<li><a href="../company/detail?ticker='+searchResult[key].ticker+'">'+
                                       searchResult[key].ticker+'.'+
                                       searchResult[key].house+'&nbsp;'+
                                       searchResult[key].name+
                                      '</a></li>';
                        }
                        $("#search_dropdown > ul").html(liHtml);
                    }
                    else {
                        $("#search_dropdown > ul").html('');
                        $("#search_dropdown").removeClass('is-open');
                    }
                }
            });
        }
        else {
            $("#search_dropdown > ul").html('');
            $("#search_dropdown").removeClass('is-open');
        }
    });
});
