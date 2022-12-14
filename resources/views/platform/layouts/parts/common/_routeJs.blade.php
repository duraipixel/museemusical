<script>
    // global app configuration object
    const config = {
       routes: {
           'roles': {
                'roles': "{{ route('roles') }}",
                'delete': "{{ route('roles.delete') }}",
                'status': "{{ route('roles.status') }}",
                'add': "{{ route('roles.add.edit') }}",
                'export': {
                    'excel': "{{ route('roles.export.excel') }}",
                    'pdf': "{{ route('roles.export.pdf') }}",
                }
           },
           'users': {
               
               'delete': "{{ route('users.delete') }}",
               'status': "{{ route('users.status') }}",
               'add': "{{ route('users.add.edit') }}",
               'export': {
                   'excel': "{{ route('users.export.excel') }}",
                   'pdf': "{{ route('users.export.pdf') }}",
               }
            },
            'order-status': {
               
               'delete': "{{ route('order-status.delete') }}",
               'status': "{{ route('order-status.status') }}",
               'add': "{{ route('order-status.add.edit') }}",
               'export': {
                   'excel': "{{ route('order-status.export.excel') }}",
                   'pdf': "{{ route('order-status.export.pdf') }}",
               }
            },
            'country': {
               
               'delete': "{{ route('country.delete') }}",
               'status': "{{ route('country.status') }}",
               'add': "{{ route('country.add.edit') }}",
               'export': {
                   'excel': "{{ route('country.export.excel') }}",
                   'pdf': "{{ route('country.export.pdf') }}",
               }
            },
            'state': {
               
               'delete': "{{ route('state.delete') }}",
               'status': "{{ route('state.status') }}",
               'add': "{{ route('state.add.edit') }}",
               'export': {
                   'excel': "{{ route('state.export.excel') }}",
                   'pdf': "{{ route('state.export.pdf') }}",
               }
            },
            'pincode': {
               
               'delete': "{{ route('pincode.delete') }}",
               'status': "{{ route('pincode.status') }}",
               'add': "{{ route('pincode.add.edit') }}",
               'export': {
                   'excel': "{{ route('pincode.export.excel') }}",
                   'pdf': "{{ route('pincode.export.pdf') }}",
               }
            },
            'city': {
               
               'delete': "{{ route('city.delete') }}",
               'status': "{{ route('city.status') }}",
               'add': "{{ route('city.add.edit') }}",
               'export': {
                   'excel': "{{ route('city.export.excel') }}",
                   'pdf': "{{ route('city.export.pdf') }}",
               }
            },
            'brands': {
               
               'delete': "{{ route('brands.delete') }}",
               'status': "{{ route('brands.status') }}",
               'add': "{{ route('brands.add.edit') }}",
               'export': {
                   'excel': "{{ route('brands.export.excel') }}",
                   'pdf': "{{ route('brands.export.pdf') }}",
               }
            },
            'main_category': {
               
               'delete': "{{ route('main_category.delete') }}",
               'status': "{{ route('main_category.status') }}",
               'add': "{{ route('main_category.add.edit') }}",
               'export': {
                   'excel': "{{ route('main_category.export.excel') }}",
                   'pdf': "{{ route('main_category.export.pdf') }}",
               }
            },          
            'sub_category': {
               
               'delete': "{{ route('sub_category.delete') }}",
               'status': "{{ route('sub_category.status') }}",
               'add': "{{ route('sub_category.add.edit') }}",
               'export': {
                   'excel': "{{ route('sub_category.export.excel') }}",
                   'pdf': "{{ route('sub_category.export.pdf') }}",
               }
            },
            'product-tags': {
               
               'delete': "{{ route('product-tags.delete') }}",
               'status': "{{ route('product-tags.status') }}",
               'add': "{{ route('product-tags.add.edit') }}",
               'export': {
                   'excel': "{{ route('product-tags.export.excel') }}",
                   'pdf': "{{ route('product-tags.export.pdf') }}",
               }
            },
            'product-labels': {
               
                'delete': "{{ route('product-labels.delete') }}",
                'status': "{{ route('product-labels.status') }}",
                'add': "{{ route('product-labels.add.edit') }}",
                'export': {
                   'excel': "{{ route('product-labels.export.excel') }}",
                   'pdf': "{{ route('product-labels.export.pdf') }}",
                }
            },
            'testimonials': {
               
               'delete': "{{ route('testimonials.delete') }}",
               'status': "{{ route('testimonials.status') }}",
               'add': "{{ route('testimonials.add.edit') }}",
               'export': {
                   'excel': "{{ route('testimonials.export.excel') }}",
                   'pdf': "{{ route('testimonials.export.pdf') }}",
               }
            },
            'products': {
               'delete': "{{ route('products.delete') }}",
               'status': "{{ route('products.status') }}",
               'add': "{{ route('products.add.edit') }}",
               'export': {
                   'excel': "{{ route('products.export.excel') }}",
                   'pdf': "{{ route('products.export.pdf') }}",
               }
            },
            'walkthroughs': {               
               'delete': "{{ route('walkthroughs.delete') }}",
               'status': "{{ route('walkthroughs.status') }}",
               'add': "{{ route('walkthroughs.add.edit') }}",
               'export': {
                   'excel': "{{ route('walkthroughs.export.excel') }}",
                   'pdf': "{{ route('walkthroughs.export.pdf') }}",
                }
            },
            'product-category': {
               'delete': "{{ route('product-category.delete') }}",
               'status': "{{ route('product-category.status') }}",
               'add': "{{ route('product-category.add.edit') }}",
               'export': {
                   'excel': "{{ route('product-category.export.excel') }}",
                   'pdf': "{{ route('product-category.export.pdf') }}",
                }
            },
            'tax': {
               'delete': "{{ route('tax.delete') }}",
               'status': "{{ route('tax.status') }}",
               'add': "{{ route('tax.add.edit') }}",
               'export': {
                   'excel': "{{ route('tax.export.excel') }}",
                   'pdf': "{{ route('tax.export.pdf') }}",
                }
            },
            'coupon': {
               'delete': "{{ route('coupon.delete') }}",
               'status': "{{ route('coupon.status') }}",
               'add': "{{ route('coupon.add.edit') }}",
               'export': {
                   'excel': "{{ route('coupon.export.excel') }}",
                   'pdf': "{{ route('coupon.export.pdf') }}",
                }
            },
            'discount': {
               'delete': "{{ route('discount.delete') }}",
               'status': "{{ route('discount.status') }}",
               'add': "{{ route('discount.add.edit') }}",
               'export': {
                   'excel': "{{ route('discount.export.excel') }}",
                   'pdf': "{{ route('discount.export.pdf') }}",
                }
            },
            'email-template': {
               'delete': "{{ route('email-template.delete') }}",
               'status': "{{ route('email-template.status') }}",
               'add': "{{ route('email-template.add.edit') }}",
               
            },   
            'customer': {
               'delete': "{{ route('customer.delete') }}",
               'status': "{{ route('customer.status') }}",
               'add': "{{ route('customer.add.edit') }}",
               'export': {
                   'excel': "{{ route('customer.export.excel') }}",
                   'pdf': "{{ route('customer.export.pdf') }}",
                }
               
            },   
            'video-booking': {
               'delete': "{{ route('video-booking.delete') }}",
               'status': "{{ route('video-booking.status') }}",
               'add': "{{ route('video-booking.add.edit') }}",
               'export': {
                   'excel': "{{ route('video-booking.export.excel') }}",
                   'pdf': "{{ route('video-booking.export.pdf') }}",
               }
            },  
            'product-attribute': {
               'delete': "{{ route('product-attribute.delete') }}",
               'status': "{{ route('product-attribute.status') }}",
               'add': "{{ route('product-attribute.add.edit') }}",
               'export': {
                   'excel': "{{ route('product-attribute.export.excel') }}",
                   'pdf': "{{ route('product-attribute.export.pdf') }}",
                }
            },    
            'product-collection': {
               'delete': "{{ route('product-collection.delete') }}",
               'status': "{{ route('product-collection.status') }}",
               'add': "{{ route('product-collection.add.edit') }}",
               'export': {
                   'excel': "{{ route('product-collection.export.excel') }}",
                   'pdf': "{{ route('product-collection.export.pdf') }}",
                }
            },    
            'sms-template': {
               'delete': "{{ route('sms-template.delete') }}",
               'status': "{{ route('sms-template.status') }}",
               'add': "{{ route('sms-template.add.edit') }}",
               'export': {
                   'excel': "{{ route('sms-template.export.excel') }}",
                   'pdf': "{{ route('sms-template.export.pdf') }}",
                }
            },  
            'payment-gateway': {
               'delete': "{{ route('payment-gateway.delete') }}",
               'status': "{{ route('payment-gateway.status') }}",
               'add': "{{ route('payment-gateway.add.edit') }}",
               'export': {
                   'excel': "{{ route('payment-gateway.export.excel') }}",
                   'pdf': "{{ route('payment-gateway.export.pdf') }}",
                }
            },      
         
            'banner': {
               'delete': "{{ route('banner.delete') }}",
               'status': "{{ route('banner.status') }}",
               'add': "{{ route('banner.add.edit') }}",
               'export': {
                   'excel': "{{ route('banner.export.excel') }}",
                   'pdf': "{{ route('banner.export.pdf') }}",
                }
            },  
            
            'newsletter': {
               'delete': "{{ route('newsletter.delete') }}",
               'status': "{{ route('newsletter.status') }}",
               'add': "{{ route('newsletter.add.edit') }}",
               'export': {
                   'excel': "{{ route('newsletter.export.excel') }}",
                   'pdf': "{{ route('newsletter.export.pdf') }}",
                }
            },  
                
       }
   };
</script>