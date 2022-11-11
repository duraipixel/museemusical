"use strict";

// Class definition
var KTAppEcommerceSaveProduct = function () {

    // Private functions

    // Init quill editor
    const initQuill = () => {
        // Define all elements for quill editor
        const elements = [
            '#kt_ecommerce_add_product_meta_description',
            '#kt_ecommerce_add_product_short_description',
            '#kt_ecommerce_add_product_long_description',
            '#kt_ecommerce_add_product_technical_specification',
            '#kt_ecommerce_add_product_specification',
            '#kt_ecommerce_add_product_features',
            '#kt_ecommerce_add_product_benefits',
            '#kt_ecommerce_add_product_content',

        ];

        $('#kt_product_sale_end_date').flatpickr({
            altInput: true,
            altFormat: "d F, Y",
            dateFormat: "Y-m-d",
        });
        $('#kt_product_sale_start_date').flatpickr({
            altInput: true,
            altFormat: "d F, Y",
            dateFormat: "Y-m-d",
        });

        // Loop all elements
        elements.forEach(element => {
            // Get quill element
            let quill = document.querySelector(element);

            // Break if element not found
            if (!quill) {
                return;
            }

            // Init quill --- more info: https://quilljs.com/docs/quickstart/
            quill = new Quill(element, {
                modules: {
                    toolbar: [
                        [{
                            header: [1, 2, false]
                        }],
                        ['bold', 'italic', 'underline'],
                        ['image', 'code-block']
                    ]
                },
                placeholder: 'Type your text here...',
                theme: 'snow' // or 'bubble'
            });
        });
    }

    // Init tagify
    const initTagify = () => {
        // Define all elements for tagify
        const elements = [
            '#kt_ecommerce_add_product_category',
            '#kt_ecommerce_add_product_tags'
        ];
        // Loop all elements
        elements.forEach(element => {
            // Get tagify element
            const tagify = document.querySelector(element);
            // Break if element not found
            if (!tagify) {
                return;
            }
            // Init tagify --- more info: https://yaireo.github.io/tagify/
            new Tagify(tagify, {
                whitelist: ["new", "trending", "sale", "discounted", "selling fast", "last 10"],
                dropdown: {
                    maxItems: 20,           // <- mixumum allowed rendered suggestions
                    classname: "tagify__inline__suggestions", // <- custom classname for this dropdown, so it could be targeted
                    enabled: 0,             // <- show suggestions on focus
                    closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
                }
            });
        });
    }
    // Init form repeater --- more info: https://github.com/DubFriend/jquery.repeater
    const initFormRepeater = () => {
        $('#kt_ecommerce_add_product_options').repeater({
            initEmpty: false,
            defaultValues: {
                'text-input': 'foo'
            },
            show: function () {
                $(this).slideDown();
                // Init select2 on new repeated items
                initConditionsSelect2();
            },
            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });
    }

    // Init condition select2
    const initConditionsSelect2 = () => {
        // Tnit new repeating condition types
        const allConditionTypes = document.querySelectorAll('[data-kt-ecommerce-catalog-add-product="product_option"]');
        allConditionTypes.forEach(type => {
            if ($(type).hasClass("select2-hidden-accessible")) {
                return;
            } else {
                $(type).select2({
                    minimumResultsForSearch: -1
                });
            }
        });
    }
    // Init noUIslider
    const initSlider = () => {
        var slider = document.querySelector("#kt_ecommerce_add_product_discount_slider");
        var value = document.querySelector("#kt_ecommerce_add_product_discount_label");

        noUiSlider.create(slider, {
            start: [10],
            connect: true,
            range: {
                "min": 1,
                "max": 100
            }
        });

        slider.noUiSlider.on("update", function (values, handle) {
            value.innerHTML = Math.round(values[handle]);
            var discount = document.getElementById('discount_percentage');
            discount.value = Math.round(values[handle])
            if (handle) {
                value.innerHTML = Math.round(values[handle]);
            }
        });
    }

    // Handle discount options
    const handleDiscount = () => {
        const discountOptions = document.querySelectorAll('input[name="discount_option"]');
        const salePriceEL = document.getElementById('kt_ecommerce_add_product_sale_price');
        const percentageEl = document.getElementById('kt_ecommerce_add_product_discount_percentage');
        const fixedEl = document.getElementById('kt_ecommerce_add_product_discount_fixed');

        discountOptions.forEach(option => {
            option.addEventListener('change', e => {
                const value = e.target.value;

                switch (value) {
                    case '2': {
                        percentageEl.classList.remove('d-none');
                        salePriceEL.classList.remove('d-none');
                        fixedEl.classList.add('d-none');
                        break;
                    }
                    case '3': {
                        percentageEl.classList.add('d-none');
                        salePriceEL.classList.remove('d-none');
                        fixedEl.classList.remove('d-none');
                        break;
                    }
                    default: {
                        percentageEl.classList.add('d-none');
                        fixedEl.classList.add('d-none');
                        salePriceEL.classList.add('d-none');
                        break;
                    }
                }
            });
        });
    }

    const handleTaxDiscount = () => {
        const discountOptions = document.querySelectorAll('input[name="discount_tax_option"]');
        const percentageEl = document.getElementById('kt_ecommerce_add_product_tax_fixed');
        // const fixedEl = document.getElementById('kt_ecommerce_add_product_discount_fixed');

        discountOptions.forEach(option => {
            option.addEventListener('change', e => {
                const value = e.target.value;

                switch (value) {
                    case '2': {
                        percentageEl.classList.remove('d-none');
                        // fixedEl.classList.add('d-none');
                        break;
                    }
                    default: {
                        percentageEl.classList.add('d-none');
                        // fixedEl.classList.add('d-none');
                        break;
                    }
                }
            });
        });
    }

    // Shipping option handler
    const handleShipping = () => {
        const shippingOption = document.getElementById('kt_ecommerce_add_product_shipping_checkbox');
        const shippingForm = document.getElementById('kt_ecommerce_add_product_shipping');

        shippingOption.addEventListener('change', e => {
            const value = e.target.checked;

            if (value) {
                shippingForm.classList.remove('d-none');
            } else {
                shippingForm.classList.add('d-none');
            }
        });
    }

    // Category status handler
    const handleStatus = () => {
        const target = document.getElementById('kt_ecommerce_add_product_status');
        const select = document.getElementById('kt_ecommerce_add_product_status_select');
        const statusClasses = ['bg-success', 'bg-warning', 'bg-danger'];

        $(select).on('change', function (e) {
            const value = e.target.value;

            switch (value) {
                case "published": {
                    target.classList.remove(...statusClasses);
                    target.classList.add('bg-success');
                    hideDatepicker();
                    break;
                }
                case "scheduled": {
                    target.classList.remove(...statusClasses);
                    target.classList.add('bg-warning');
                    showDatepicker();
                    break;
                }
                case "inactive": {
                    target.classList.remove(...statusClasses);
                    target.classList.add('bg-danger');
                    hideDatepicker();
                    break;
                }
                case "draft": {
                    target.classList.remove(...statusClasses);
                    target.classList.add('bg-primary');
                    hideDatepicker();
                    break;
                }
                default:
                    break;
            }
        });


        // Handle datepicker
        const datepicker = document.getElementById('kt_ecommerce_add_product_status_datepicker');

        // Init flatpickr --- more info: https://flatpickr.js.org/
        $('#kt_ecommerce_add_product_status_datepicker').flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });

        const showDatepicker = () => {
            datepicker.parentNode.classList.remove('d-none');
        }

        const hideDatepicker = () => {
            datepicker.parentNode.classList.add('d-none');
        }
    }

    // Condition type handler
    const handleConditions = () => {
        const allConditions = document.querySelectorAll('[name="method"][type="radio"]');
        const conditionMatch = document.querySelector('[data-kt-ecommerce-catalog-add-category="auto-options"]');
        allConditions.forEach(radio => {
            radio.addEventListener('change', e => {
                if (e.target.value === '1') {
                    conditionMatch.classList.remove('d-none');
                } else {
                    conditionMatch.classList.add('d-none');
                }
            });
        })
    }

    // Submit form handler
    const handleSubmit = () => {
        // Define variables
        let validator;
        // Get elements
        const form = document.getElementById('kt_ecommerce_add_product_form');
        const submitButton = document.getElementById('kt_ecommerce_add_product_submit');
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'product_name': {
                        validators: {
                            notEmpty: {
                                message: 'Product Name is required'
                            }
                        }
                    },
                    'category_id': {
                        validators: {
                            notEmpty: {
                                message: 'Category is required'
                            }
                        }
                    },
                    'brand_id': {
                        validators: {
                            notEmpty: {
                                message: 'Brand is required'
                            }
                        }
                    },
                    'label_id': {
                        validators: {
                            notEmpty: {
                                message: 'Product Label is required'
                            }
                        }
                    },
                    'tag_id': {
                        validators: {
                            notEmpty: {
                                message: 'Product Tag is required'
                            }
                        }
                    },
                    'base_price': {
                        validators: {
                            notEmpty: {
                                message: 'Base Price is required'
                            }
                        }
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    }),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'fa fa-check',
                        invalid: 'fa fa-times',
                        validating: 'fa fa-refresh',
                    }),
                }
            }
        );

        // Handle submit button
        submitButton.addEventListener('click', e => {
            e.preventDefault();
            // Validate form before submit
            if (validator) {
                validator.validate().then(function (status) {
                    console.log('validated!');
                    if (status == 'Valid') {
                        //session set for productid to store iamges
                        var form = $('#kt_ecommerce_add_product_form')[0]; 
                        var formData = new FormData(form);                                       
                        
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });  
                        $.ajax({
                            url: add_url,
                            type:"POST",
                            data: formData,
                            contentType: false,
                            cache: false,
                            processData:false,
                            beforeSend: function() {
                                submitButton.setAttribute('data-kt-indicator', 'on');
                                submitButton.disabled = true;
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
                                    
                                    if( res.product_id ) {
                                               
                                        myDropzone.processQueue();
                                        myDropzone.on("addedfiles", (file) => {
                                           console.log( myDropzone.hiddenFileInput );
                                       });

                                       myBrocheureDropzone.processQueue();

                                    }

                                    submitButton.removeAttribute('data-kt-indicator');
                                     // Enable button
                                    submitButton.disabled = false;

                                    Swal.fire({
                                        // text: "Form has been successfully submitted!",
                                        text: "Thank you! You've updated Products",
                                        icon: "success",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn btn-primary"
                                        }
                                    }).then(function (result) {
                                        if (result.isConfirmed) {
                                           
                                            
                                        }
                                    });
                                }
                            }
                        });
                       
                    } else {
                        Swal.fire({
                            html: "Sorry, looks like there are some errors detected, please try again. <br/><br/>Please note that there may be errors in the <strong>General</strong> or <strong>Advanced</strong> tabs",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }
                });
            }
        })
    }

    // Public methods
    return {
        init: function () {
            // Init forms
            initQuill();
            initTagify();
            initSlider();
            initFormRepeater();
            initConditionsSelect2();
            // Handle forms
            handleStatus();
            handleConditions();
            handleDiscount();
            handleTaxDiscount();
            handleShipping();
            handleSubmit();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTAppEcommerceSaveProduct.init();
});
