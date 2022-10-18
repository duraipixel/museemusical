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
          'product': {
               
               'delete': "{{ route('product.delete') }}",
               'status': "{{ route('product.status') }}",
               'add': "{{ route('product.add.edit') }}",
               'export': {
                   'excel': "{{ route('product.export.excel') }}",
                   'pdf': "{{ route('product.export.pdf') }}",
               }
          },
          'walk_throughs': {
               
               'delete': "{{ route('walk_throughs.delete') }}",
               'status': "{{ route('walk_throughs.status') }}",
               'add': "{{ route('walk_throughs.add.edit') }}",
               'export': {
                   'excel': "{{ route('walk_throughs.export.excel') }}",
                   'pdf': "{{ route('walk_throughs.export.pdf') }}",
               }
          },
       
                
       }
   };
</script>