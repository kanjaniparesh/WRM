@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ $url }}/home">Back</a>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form id="tinyurl" role="form" autocomplete="false">
                <div class="col-md-12">
                    @csrf
                    <input type="hidden" name="tiny_id" id="tiny_id" value="{{$data->id}}">

                    <div class="form-group">
                        <label for="url">URL</label>
                        <input type="text" class="form-control" value="{{$data->link}}" id="url" name="url" aria-describedby="urlHelp" autocomplete="off" placeholder="Enter url">
                        <label id="cust-url-error" for="url" style="color:red;display:none;"></label>
                        <small id="urlHelp" class="form-text text-muted">We'll never share your tiny url with anyone else.</small>
                    </div>
                    <div class="form-group pull-right">
                        <input type="submit" class="btn pull-right btn-primary" value="Save">
                    </div>
                </div>
            </form>
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
</script>
@endsection