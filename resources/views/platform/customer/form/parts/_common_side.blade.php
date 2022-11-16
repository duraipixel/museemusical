<style>
    .circular--landscape {
         display: inline-block;
        position: relative;
        width: 200px;
        height: 200px;
        overflow: hidden;
        border-radius: 50%;
    } 
    .circular--landscape img {
        width: auto;
        height: 100%;
        margin-left: -50px; 
    }
</style>
<div class="card card-flush" >
    <div class="card-header">
        <div class="card-title w-100">
            <h2 class="w-100">
                Customer Information
            </h2>
        </div>
    </div>
   
    <div class="card-body pt-0 fv-row" style="align-self: center;">
        <strong for="">{{  $info->first_name." ".$info->last_name}}</strong>
   
        <br>
        <label for="">{{  $info->customer_no}}</label>
        <br>

        <label for="">{{  $info->email}}</label>
        <br>

        <label for="">{{  $info->mobile_no}}</label>
        <br>
    </div>
    <div class="card-body pt-0 fv-row">
        @if ($info->profile_image ?? '')
        @php 
            $path = Storage::url($info->profile_image,'public')
        @endphp
        <img src="{{ asset($path) }}" class="circular--landscape" alt="Avatar">
        @endif
    </div>
</div>
