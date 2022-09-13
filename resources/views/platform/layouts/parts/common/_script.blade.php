@include('platform.layouts.parts.common._routeJs')
<script>
    
    const element = document.getElementById('kt_modal_export');
    const exportModal = new bootstrap.Modal(element);

    function openExportForm(export_type) {
        console.log( export_type );
        $('#export_type').val( export_type );
        $('#export_modal_title').html( 'EXPORT '+ export_type.toUpperCase() );
        exportModal.show();
    }

    $('.export_modal_close').click(function(){
        exportModal.hide();
    })

    function openForm(module_type, id = '') {
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.ajax({
            url: config.routes[module_type].add,
            type: 'POST',
            data: {id:id},
            success: function(res) {
                $( '#form-common-content' ).html(res);
                const drawerEl = document.querySelector("#kt_common_add_form");
                const commonDrawer = KTDrawer.getInstance(drawerEl);
                commonDrawer.show();
                
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
                            text: "You deleted the role!",
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
                            text: "You changed the role status!",
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
</script>