Global = new Object();
//for search
Global.hold = false;

$(document).ready(function(){
    $("#search_company").val('');
    $("#search_company").bind('input propertychange',function(){
        if($(this).val()) {
            if(Global.hold){
                Global.searchRequest.abort();
            }
            Global.searchRequest = $.ajax({
                type:'GET',
                url: '../data/search',
                beforeSend:function(){
                    Global.hold = true;
                },
                data: {
                    "text":$("#search_company").val(),
                },
                success: function(result) {
                    if($("#search_company").val()) {
                        var searchResult = eval('('+result+')');
                        var liHtml = "<ul>";
                        for(var key in searchResult){
                            liHtml += '<li><a href="../company/detail?ticker='+searchResult[key].ticker+'">'+
                                       searchResult[key].ticker+'.'+
                                       searchResult[key].house+'&nbsp;'+
                                       searchResult[key].name+
                                      '</a></li>';
                        }
                        liHtml += "</ul>";
                        if(liHtml !="<ul></ul>") {
                            $("#search_dropdown").html(liHtml);
                            $("#search_dropdown").addClass('is-open');
                        }
                        else {
                            $("#search_dropdown").html('');
                            $("#search_dropdown").removeClass('is-open');
                        }
                    }
                    else {
                        $("#search_dropdown").html('');
                        $("#search_dropdown").removeClass('is-open');
                    }
                    Global.hold = false;
                }
            });
        }
        else {
            $("#search_dropdown").html('');
            $("#search_dropdown").removeClass('is-open');
        }
    });
});
