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
          'brand': {
               
               'delete': "{{ route('brand.delete') }}",
               'status': "{{ route('brand.status') }}",
               'add': "{{ route('brand.add.edit') }}",
               'export': {
                   'excel': "{{ route('brand.export.excel') }}",
                   'pdf': "{{ route('brand.export.pdf') }}",
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
       
                
       }
   };
</script>