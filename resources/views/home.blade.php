@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div> -->
            <form id="tinyurl" role="form" autocomplete="false">
                <div class="col-md-12">
                    @csrf

                    <div class="form-group">
                        <label for="url">URL</label>
                        <input type="text" class="form-control" id="url" name="url" aria-describedby="urlHelp" autocomplete="off" placeholder="Enter url">
                        <label id="cust-url-error" for="url" style="color:red;display:none;"></label>
                        <small id="urlHelp" class="form-text text-muted">We'll never share your tiny url with anyone else.</small>
                    </div>
                    <div class="form-group pull-right">
                        <input type="submit" class="btn pull-right btn-primary" value="Save">
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-11">
            <h1>My Links</h1>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:35%">Link</th>
                        <th style="width:15%">Created On</th>
                        <th style="width:10%">Disabled</th>
                        <th style="width:15%">Visitor Count</th>
                        <th scope="width:25%">Action</th>
                    </tr>
                </thead>
                <tbody id="url_list">
                    @if(!empty($data) && $data->count())
                    @foreach($data as $key => $value)
                    <tr>
                        <td style="word-break:break-all;">{{ $value->link }}</td>
                        <td>{{ $value->created_at }}</td>
                        <td>{{ $value->disable }}</td>
                        <td>{{ $value->visit_count }}</td>
                        <td>
                            <a class="btn btn-outline-secondary" href="{{ route('s',[$value->code])}}" target="_blank">View</a>
                            <a class="btn btn-outline-primary" href="{{ route('editurl',[$value->id])}}">Edit</a>
                            <button type="button" class="btn btn-outline-danger remove_url" data-val="{{$value->id}}">Delete</button>
                           
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="10">There are no data.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            {!! $data->links() !!}
        </div>
    </div>
</div>
<div style="display: none;" id="urllistBodyDetails">
    <table>
        <tr id="PlaceHolderRow">
            <td id="url_id"></td>
            <td id="url_link"></td>
            <td id="url_created"></td>
            <td id="url_enable"></td>
            <td id="url_view"></td>
        </tr>
    </table>
</div>
<div class="modal" tabindex="-1" role="dialog" id="openmodal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete URL</h5>
        
      </div>
      <div class="modal-body">
        <p>Are you sure ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="modalYes">Yes</button>
        <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')

<script>
    var url = '{{route("saveurl") }}';
    $(document).ready(function() {

        $(".backend-error").addClass('no-show')
        $(".backend-error").html('')
        $('#tinyurl').validate({
            errorClass: 'jv-error',
            ignore: [],
            rules: {
                'url': {
                    required: true,
                },
            },
            messages: {
                'name': {
                    required: 'Please enter url',
                },
            },
            submitHandler: function(form, e) {
                e.preventDefault();
                var formdata = new FormData(form);
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formdata,
                    dataType: "json",
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success == 1) {
                            $('#tinyurl').trigger("reset");
                            var url = '{{route("geturl",["code"]) }}';
                            url = url.replace("code", response.code);
                            window.location.href = url;
                        } else {

                        }
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status === 422) {
                            var errors = JSON.parse(xhr.responseText);
                            if (errors.message) {
                                $('#cust-url-error').show()
                                $('#cust-url-error').text(errors.message)
                            }
                        }
                    }
                });
            }
        });

    });
    $('.remove_url').on('click', function(e) {
        var tiny_id = $(this).attr('data-val');
        // DeleteData(tiny_id, 'Delete')
        $('#openmodal').modal('show');
        var deletefun="DeleteData("+tiny_id+",'Delete')"
        $('#modalYes').attr('onclick',deletefun)
        
    });
    function closeModal()
    {
        $('#openmodal').modal('hide');
    }
    function DeleteData(tiny_id, action) {
        $('#openmodal').modal('hide');
        $.ajax({
            url: '{{ route("deleteurl") }}',
            type: 'POST',
            data: {
                'id': tiny_id,
                '_token': '{{ csrf_token() }}',
                'action': action
            },
            success: function(response) {
                if (response.success == "1") {
                    alert(action + "d !")
                } else {
                    alert("Soory, Please try after sometime !")
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr);
                if (xhr.status === 422) {
                    alert("Soory, Please try after sometime !")
                }
            },
            complete: function() {
                location.reload();
            }
        });
    }
</script>
@endsection