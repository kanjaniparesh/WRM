@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-5" id="piechart">
        </div>
        <div class="col-md-2" >
        </div>
        <div class="col-md-5" style="min-height:350px;background-color:white;">
        <div style="text-align:center;font-size:24px;font-weight:500;">Top 3 most visited URL</div>    
        <table class="table">
                <thead>
                    <tr>
                        <th>Url</th>
                        <th>Visted Count</th>
                        <th>Created On</th>
                    </tr>
                </thead>
                <tbody id="highCountTbody">

                </tbody>
            </table>
        </div>
    </div>
    <div class="row justify-content-center">

        <div class="col-md-12">
            <h1>All Links</h1>
            <table class="table table-fixed">
                <thead>
                    <tr>
                        <th style="width:15%">UserName</th>
                        <th style="width:35%">Link</th>
                        <th style="width:10%">Created On</th>
                        <th style="width:10%">Disabled</th>
                        <th style="width:30%">Action</th>
                    </tr>
                </thead>
                <tbody id="url_list">
                    @if(!empty($data) && $data->count())
                    
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value->users->name}}</td>
                        <td style="word-break:break-all;">{{ $value->link }}</td>
                        <td>{{ $value->created_at }} </td>
                        <td>{{ $value->disable }}</td>
                        <td>
                            @if($value->disable == 'Yes')
                            <button class="btn btn-outline-primary" style="cursor: no-drop;">Edit</button>
                            <button type="button" class="btn btn-outline-secondary" style="cursor: no-drop;">Disabled</button>
                            @else
                            <a class="btn btn-outline-primary" href="{{ route('editurl',[$value->id])}}">Edit</a>
                            <button type="button" class="btn btn-outline-secondary disable_url" data-val="{{$value->id}}">Disabled</button>
                            @endif
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
            <td id="url_link"></td>
            <td id="url_count"></td>
            <td id="url_created"></td>
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
    $(document).ready(function() {
        getPieChart()
    });
    $('.remove_url').on('click', function(e) {
        var tiny_id = $(this).attr('data-val');
        // DeleteData(tiny_id, 'Delete')
        $('.modal-title').html("Delete URL")
        $('#openmodal').modal('show');

        var deletefun="DeleteData("+tiny_id+",'Delete')"
        $('#modalYes').attr('onclick',deletefun)

    });
    $('.disable_url').on('click', function(e) {
        var tiny_id = $(this).attr('data-val');
        $('.modal-title').html("Disable URL")

        // DeleteData(tiny_id, 'Disable')
        $('#openmodal').modal('show');
        var deletefun="DeleteData("+tiny_id+",'Disable')"
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

    function getPieChart() {
        $.ajax({
            url: '{{ route("piechart") }}',
            type: 'GET',
            success: function(response) {
                console.log(response.data)
                drawPieChart(response.data)
                drawHighTable(response)
            },
            error: function(xhr, status, error) {
                console.log(xhr);
                if (xhr.status === 422) {
                    alert("Soory, Please try after sometime !")
                }
            },
        });
    }

    function drawPieChart(data) {
        Highcharts.chart('piechart', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Users Data'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            credits: {
                enabled: false
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    }
                }
            },
            series: [{
                name: 'Usage',
                colorByPoint: true,
                data: data.data
            }]
        });
    }

    function drawHighTable(data) {
        var count = 0;
        $("#highCountTbody").html('');
            
        $.each(data.tableData, function($key, $val) {
            var $Bodycontent = $("#urllistBodyDetails").clone();
                $Bodycontent.find("#url_link").html(data.APP_URL+'/s/'+$val['code']);
                $Bodycontent.find("#url_count").html($val['visit_count']);
                $Bodycontent.find("#url_created").html($val['created_at']);
            
            $("#highCountTbody").append($Bodycontent.find("#PlaceHolderRow"));

        })
    }
</script>
@endsection