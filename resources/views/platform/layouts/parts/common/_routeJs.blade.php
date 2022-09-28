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
       }
   };
</script>