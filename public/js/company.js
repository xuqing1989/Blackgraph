var companyObj = new Object();

companyObj.filter = {'industry':'all',
                     'subindustry':'all',
                     'flag':'all',
                     'page':1};

companyObj.report_list = function(){
    $('#ajax_report_list').html('');
    $('#ajax_loader').show();
    $.ajax({
        type:'GET',
        url: '../data/report',
        data: {
            "date":'all',
            "industry":companyObj.filter.industry,
            "subindustry":companyObj.filter.subindustry,
            "flag":companyObj.filter.flag,
            "page":companyObj.filter.page,
        },
        success: function(result) {
            $('#ajax_loader').hide();
            $('#ajax_report_list').html(result);
            $('.list_page_num').click(function() {
                var num = $(this).html();
                if(num != '...') {
                    companyObj.filter.page = num;
                }
                companyObj.report_list();
            });
            $('#list_page_pre').click(function(){
                companyObj.filter.page--;
                companyObj.report_list();
            });
            $('#list_page_next').click(function(){
                companyObj.filter.page++;
                companyObj.report_list();
            });
        }
    });
}

companyObj.preLoad = function(iid,sid) {
    companyObj.filter.industry = iid;
    if(iid=='all') {
        companyObj.filter.subindustry = 'all';
    }
    else {
        companyObj.filter.subindustry = sid;
        var industryDOM = $('.industry_list_select[sid='+iid+']');
        var industryName = industryDOM.html();
        var sublist = industryDOM.attr('sub');
        sublist = eval('('+sublist+')');
        var sublistHTML = '<li class="subindustry_list_select" sid="all">全部</li>';
        for(var i=0;i<sublist.length;i++) {
            sublistHTML += '<li sid="'+sublist[i]['id']+'" class="subindustry_list_select">' + sublist[i]['name']+'</li>';
        }
        $('#subindustry_list').html(sublistHTML);
        $('.subindustry_list_select').click(function(){
            $("#subindustry_title").html($(this).html()+'&nbsp;&#9660;');
            $("#subindustry_dropdown").removeClass('is-open');
            companyObj.filter.subindustry=$(this).attr('sid');
            companyObj.filter.page = 1;
            companyObj.report_list();
        });
        var subindustryDOM = $('.subindustry_list_select[sid='+sid+']');
        $('#industry_dropdown').removeClass('is-open');
        $("#subindustry_title").html(subindustryDOM.html()+'&nbsp;&#9660;');
        $('#industry_title').html(industryName+'&nbsp;&#9660;');
        companyObj.filter.page = 1;
    }
    companyObj.report_list();
}

$(document).ready(function(){
    $("#menu_title_company").addClass('actived');

    $(".industry_list_select[sid=125]").html();

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
            companyObj.filter.subindustry=$(this).attr('sid');
            companyObj.filter.page = 1;
            companyObj.report_list();
        });
        $('#industry_dropdown').removeClass('is-open');
        $("#subindustry_title").html('全部&nbsp;&#9660;');
        $('#industry_title').html(industryName+'&nbsp;&#9660;');
        companyObj.filter.industry=$(this).attr('sid');
        companyObj.filter.subindustry='all';
        companyObj.filter.page = 1;
        companyObj.report_list();
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
        companyObj.filter.industry='all';
        companyObj.filter.subindustry='all';
        companyObj.filter.page = 1;
        companyObj.report_list();
    });

    $('#flag_checkbox').attr('checked',false);


    $('#flag_checkbox').click(function(){
        if($(this).is(':checked')){
            companyObj.filter.flag=1;
        }
        else {
            companyObj.filter.flag='all';
        }
        companyObj.filter.page = 1;
        companyObj.report_list();
    });

    companyObj.preLoad(Global.industry,Global.subindustry);
});
