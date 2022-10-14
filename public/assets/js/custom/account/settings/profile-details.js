"use strict";

// Class definition
var KTAccountSettingsProfileDetails = function () {
    // Private variables
    var form;
    var submitButton;
    var validation;

    // Private functions
    var initValidation = function () {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validation = FormValidation.formValidation(
            form,
            {
                fields: {
                    site_name: {
                        validators: {
                            notEmpty: {
                                message: 'Site name is required'
                            }
                        }
                    },
                    site_mobile_no: {
                        validators: {
                            notEmpty: {
                                message: 'Contact phone number is required'
                            }
                        }
                    },
                    site_email: {
                        validators: {
                            notEmpty: {
                                message: 'Contact Email is required'
                            }
                        }
                    },
                    company: {
                        validators: {
                            notEmpty: {
                                message: 'Company name is required'
                            }
                        }
                    },
                    
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    //defaultSubmit: new FormValidation.plugins.DefaultSubmit(), // Uncomment this line to enable normal button submit after form validation
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    })
                }
            }
        );

        const submitButton = document.querySelector('#kt_account_global_submit');
        submitButton.addEventListener('click', function (e) {
            e.preventDefault();
            console.log( global_form_submit_url );
            validation.validate().then(function (status) {
                if (status == 'Valid') {

                    var form = $('#kt_account_global_form')[0]; 
                    var formData = new FormData(form);

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });  
                    $.ajax({
                        url: global_form_submit_url,
                        type:"POST",
                        data: formData,
                        contentType: false,
                        cache: false,
                        processData:false,
                        beforeSend: function() {
                        },
                        success: function(res) {
                            if( res.error == 1 ) {
                                // Remove loading indication
                                submitButton.removeAttribute('data-kt-indicator');
                                 // Enable button
                                submitButton.disabled = false;
                                let error_msg = res.message
                                Swal.fire({
                                    text: res.message,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                 });
                            } else {

                                Swal.fire({
                                    // text: "Form has been successfully submitted!",
                                    text: "Thank you! You've updated your basic info",
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(function (result) {
                                    if (result.isConfirmed) {
                                        console.log('teste');   
                                        window.location.reload();                                     
                                    }
                                });
                            }
                        }
                    });

                    // swal.fire({
                    //     text: "Thank you! You've updated your basic info",
                    //     icon: "success",
                    //     buttonsStyling: false,
                    //     confirmButtonText: "Ok, got it!",
                    //     customClass: {
                    //         confirmButton: "btn fw-bold btn-light-primary"
                    //     }
                        
                    // });
                } else {
                    swal.fire({
                        text: "Sorry, looks like there are some errors detected, please try again.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-light-primary"
                        }
                    });
                }
            });
        });
        

    }

    

    // Public methods
    return {
        init: function () {
            form = document.getElementById('kt_account_global_form');
            

            initValidation();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTAccountSettingsProfileDetails.init();
});
