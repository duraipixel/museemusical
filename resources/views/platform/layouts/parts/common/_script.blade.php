<script>
    
    const element = document.getElementById('kt_modal_export');
    const exportModal = new bootstrap.Modal(element);

    function openExportForm(export_type) {
        
        $('#export_type').val( export_type );
        $('#export_modal_title').html( 'EXPORT '+ (export_type.replace("_", " ")).toUpperCase() );
        exportModal.show();
    }

    $('.export_modal_close').click(function(){
        exportModal.hide();
    })

    function openForm(module_type, id = '', from = '', dynamicModel = '') {
               
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: config.routes[module_type].add,
            type: 'POST',
            data: {id:id, from:from, dynamicModel:dynamicModel},
            success: function(res) {
                $( '#form-common-content' ).html(res);
                const drawerEl = document.querySelector("#kt_common_add_form");
                const commonDrawer = KTDrawer.getInstance(drawerEl);
                commonDrawer.show();
                return false;
            }
        });

    }

    function commonDelete(id, module_type) {
        Swal.fire({
            text: "Are you sure you would like to delete?",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, return",
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-active-light"
            }
        }).then(function (result) {
            if (result.value) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: config.routes[module_type].delete,
                    type: 'POST',
                    data: {id:id},
                    success: function(res) {
                        dtTable.ajax.reload();
                        Swal.fire({
                            title: "Deleted!",
                            text: res.message,
                            icon: "success",
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-success"
                            },
                            timer: 3000
                        });
                        
                    }
                });		
            }
        });
    }

    function commonChangeStatus(id, status, module_type) {
        Swal.fire({
            text: "Are you sure you would like to change status?",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Yes, Change it!",
            cancelButtonText: "No, return",
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-active-light"
            }
        }).then(function (result) {
            if (result.value) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: config.routes[module_type].status,
                    type: 'POST',
                    data: {id:id, status:status},
                    success: function(res) {
                        dtTable.ajax.reload();
                        Swal.fire({
                            title: "Updated!",
                            text: res.message,
                            icon: "success",
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-success"
                            },
                            timer: 3000
                        });
                        
                    }
                });		
            }
        });
    }
    $(document).ready(function () {    
        $('.numberonly').keypress(function (e) {    
            var charCode = (e.which) ? e.which : event.keyCode    
            if (String.fromCharCode(charCode).match(/[^0-9]/g))    
                return false;                        
        });    

    }); 

    $('.mobile_num').keypress(
        function(event) {
            if (event.keyCode == 46 || event.keyCode == 8) {
                //do nothing
            } else {
                if (event.keyCode < 48 || event.keyCode > 57) {
                    event.preventDefault();
                }
            }
        }
    );
    

    function getProductCategoryDropdown(id = '' ) {
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '{{ route("common.category.dropdown") }}',
            type: 'POST',
            data: {id:id},
            success: function(res) {
                $( '#product-category' ).html(res);
                const drawerEl = document.querySelector("#kt_common_add_form");
                const commonDrawer = KTDrawer.getInstance(drawerEl);
                commonDrawer.hide();
                return false;
            }
            
        });

    }

    function getProductBrandDropdown(id = '' ) {
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '{{ route("common.brand.dropdown") }}',
            type: 'POST',
            data: {id:id},
            success: function(res) {
                const drawerEl = document.querySelector("#kt_common_add_form");
                const commonDrawer = KTDrawer.getInstance(drawerEl);
                commonDrawer.hide();
                console.log( res );
                $( '#product-category-brand' ).html(res);
            
                return false;
            }
            
        });

    }

    function getProductDynamicDropdown(id = '', tag = '' ) {
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '{{ route("common.dynamic.dropdown") }}',
            type: 'POST',
            data: {id:id, tag:tag},
            success: function(res) {
                $( '#'+tag ).html(res);
                const drawerEl = document.querySelector("#kt_common_add_form");
                const commonDrawer = KTDrawer.getInstance(drawerEl);
                commonDrawer.hide();
                return false;
            }
            
        });

    }
</script>