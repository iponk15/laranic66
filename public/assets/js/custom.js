$(document).on('click','.ajaxify',function(e){
    e.preventDefault();

    var ajaxify = [null, null, null];
    var url     = $(this).attr("href");
    var content = $('#body-content');
    
    history.pushState(null, null, url);
    if(url != ajaxify[2]){
        ajaxify.push(url);
    }

    ajaxify = ajaxify.slice(-3, 5);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var posting = $.post( url, { status_link: 'ajax' } );

    posting.done(function( data ) {
        // console.log(data);
        content.html(data);

        // set blockui
        KTApp.block(content, {});
        setTimeout(function() {
            KTApp.unblock(content);
        }, 1000);

        // set otomastis scroll top
        $('.kt-scrolltop').trigger('click');
        // var titlej   = $('.m-content .m-portlet__head-title h3 span').html();
        // var title = $('.tab-title').text();
        // var titlepage = titlej === undefined ? 'Dashboard - Wireframes' : titlej+' - Wireframes';
        // $('title').text(titlepage);
    });
});

function f_status(stat, ele, eve, dtele){
    eve.preventDefault();
    if(stat == 1){
        var mes  = "Are you sure want to change Status ?";
        var head = "Status Changed!";
        var html = false;
    }else if(stat == 2){
        var mes  = "Are you sure want to Delete data ?";
        var head = "Deleted!";
        var html = false;
    }else if(stat == 3){
        var mes  = "<b>This will delete all related Subscription too!</b></br>Are you sure want to Delete data ?";
        var head = "Deleted";
        var html = true;
    };
    // return false;
    swal.fire({
        title : "Are you sure?",
        text  : mes,
        html  : html,
        type  : "warning",
        showCancelButton   : true,
        confirmButtonColor : '#DD6B55',
        onOpen     : function() {},
        preConfirm : function(){
            return true;
        },
        confirmButtonText : 'Yes'
    }).then(function(isConfirm){
        if(isConfirm.value == true){
            var href = $(ele).attr('href');
            
            KTApp.block("#body-content",{});

            $.post(href, function(data1, textStatus, xhr) {
                KTApp.unblock("#body-content",{});
                if (data1.status == 1) {
                    swal.fire(head,data1.message,'success');
                    if (stat == 9) {
                        location.reload();
                    } else {
                        $(".reload").trigger("click");
                    }
                } else {
                    swal.fire(head, data1.message,'error');
                }
            }, 'json');
        }
    });
};