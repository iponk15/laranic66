var global = function () {
    var help_datatable = function (clas,url,column,clas_select,attribute='',eexcel='') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        var datatable = $(clas).KTDatatable({
            // datasource definition
            data         : { 
                type            : 'remote', 
                source          : { read : { url : url, method : 'POST', params : attribute } }, 
                pageSize        : 10, 
                serverPaging    : true, 
                serverFiltering : true, 
                serverSorting   : true 
            },
            layout       : { theme : 'default', scroll : false,  footer : false },
            sortable     : true,
            pagination   : true,
            drawCallback : false,
            columns      : column 
        });

        $.each(clas_select, function(k,v) {
            var a = $(v).attr('type');
            if(a == 'text'){
                $(clas_select[k]).on('keyup', function() {
                    datatable.search($(this).val().toLowerCase(), k);
                });
            }else{
                $(clas_select[k]).on('change', function() {
                    datatable.search($(this).val().toLowerCase(), k);
                });
            }
        });

        $(eexcel.button).on('click', function() {
            var dat  = $(eexcel.form).serializeArray();
            var form = '<form style="display:none;" action="'+eexcel.url+'" method="post" class="hideform">';
            for(i = 0;i < dat.length; i++){
                form = form +'<input type="text" name="'+dat[i].name+'"value="'+dat[i].value+'"></input>';
            }
            var form = form+"<form>";
            $('.forma').append(form);
            $('.hideform').submit()

        });
    };

    var help_form_validation = function (clas,rules,messages) {
        var warning      = $('.alert-warning', clas);
        var form_confirm = $(clas).attr('data-confirm');

        $( clas ).validate({
            // define validation rules
            rules    : rules,
            messages : messages,

            //display error alert on form submit  
            invalidHandler: function(event, validator) {
                var alert = $('.m_form_msg');
                alert.removeClass('m--hide').show();
                KTUtil.scrollTo(alert, -200);
            },

            submitHandler: function (form) {
                if(form_confirm == 1){
                    var mess  = "You want edit this data !";
                    var bmess = 'Yes, Edit it!';
                }else if(form_confirm == 2){
                    var mess  = "You want upload !";
                    var bmess = 'Yes, Upload!';
                }else{
                    var mess  = false;
                    var bmess = 'Yes, Submit!';
                }

                if (form_confirm === undefined) {
                    var options = { 
                        dataType : 'json',
                        success  : callback_form,
                        error    : callback_error
                    }; 

                    $(form).ajaxSubmit(options);

                } else {
                    swal.fire({
                        title             : 'Are you sure ?',
                        text              : mess,
                        type              : 'warning',
                        showCancelButton  : true,
                        confirmButtonColor: '#DD6B55',
                        confirmButtonText : bmess
                    }).then(function(result) {
                        if (result.value) {
                            var options = { 
                                dataType : 'json',
                                success  : callback_form,
                                error    : callback_error
                            }; 

                            $(form).ajaxSubmit(options);

                        }
                    });

                }

            }
        });

        function callback_form(res, statusText, xhr, $form){
            if(res.status == 1){
                warning.hide();

                toastr.options = call_toastr('4000');
                var $toast     = toastr['success'](res.message, "Success");

                if(typeof(res.custUrl) != "undefined"){
                    var init_url = base_url + res.custUrl;
                    $('.reload').attr('href',init_url);
                }
                
                if($('.reload').length)
                {
                    $('.reload').trigger('click');
                }

                $('.modal').modal('hide');
                $('.modal-backdrop').remove();

            }else if(res.status == 2){
                warning.hide();

                toastr.options = call_toastr('4000');
                var $toast     = toastr['warning'](res.message, "Warning");
            }else if(res.status == 0){
                warning.hide();

                toastr.options = call_toastr('4000');
                var $toast     = toastr['error'](res.message, "Error");
            }else{
                warning.find('span').html(res.message);
                warning.show();
            }

        }

        function callback_error(){
            toastr.options = call_toastr('4000');
            var $toast     = toastr['error']('Something wrong!', "Error");
        }

        function call_toastr(duration){
            var option = {
                "closeButton": true,
                "debug": false,
                "positionClass": "toast-top-right",
                "onclick": null,
                "showDuration": "1000",
                "hideDuration": "1000",
                "timeOut": duration,
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            return option;
        }
    }

    var hlp_dtrp = function(tipe,clas,prm){
        // 1 = datepicker, 2 = datettimepicker, 3 = timepicker, 4 = rangepicker

        if(tipe == '1'){
            $(clas).datepicker(prm);
        }else if(tipe == '2'){
            $(clas).datetimepicker(prm);
        }else if(tipe == '3'){
            $(clas).timepicker(prm);
        }else{
            $(clas).daterangepicker(prm,function(start, end, label) {
                $(clas + ' .form-control').val( start.format(prm.format) + ' - ' + end.format(prm.format));
            });
        }
    }

    var hlp_summernote = function (clas,prm) {
        // prm.dialogsInBody = true;
        $(clas).summernote(prm);
    }

    var hlp_form_repeater = function(clas,swalparam,swalResult){
        $(clas).repeater({
            initEmpty     : false,
            defaultValues : { 'text-input' : 'foo'},
            show          : function() { $(this).slideDown(); },
            hide          : function(deleteElement) {
                swal.fire(swalparam).then(function(result) {
                    if (result.value) {
                        if(swalResult != false){
                            swal.fire(
                                swalResult.title,
                                swalResult.text,
                                swalResult.type
                            );
                        }
                        $(this).slideUp(deleteElement);
                    }
                });                         
            } 
            
            // {
            //     title : 'Are you sure?',
            //     text  : "You won't be able to revert this!",
            //     type  : 'warning',
            //     showCancelButton  : true,
            //     confirmButtonText : 'Yes, delete it!'
            // }
        });
    }

    return {
        init_datatable       : function (clas,url,column,clas_select,eexcel) { help_datatable(clas,url,column,clas_select,eexcel) },
        init_form_validation : function (clas,rules,messages) { help_form_validation(clas,rules,messages) },
        init_dtrp		     : function (tipe,clas,prm) { hlp_dtrp(tipe,clas,prm) },
        init_summernote      : function (clas,prm) { hlp_summernote(clas,prm) },
        init_form_repeater   : function (clas,swalparam,swalResult) { hlp_form_repeater(clas,swalparam,swalResult) }
    }
}();