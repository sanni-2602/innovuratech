@extends('default')
@section('content')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
    @endif
    <div class="container">
        <div class="row" style="margin-bottom: 10px;">
            <div class="col-md-6" id="reportrange"
                style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 50%">
                <i class="fa fa-calendar"></i>&nbsp;
                <span>SELECT DATE RANGE</span> <i class="fa fa-caret-down"></i>
                <input type="hidden" name="start_date" id="start_date" value="">
                <input type="hidden" name="end_date" id="end_date" value="">
            </div>
            <div class="col-md-2">
                <select name="state" class="states form-control select2" id="stateId">
                    <option value="">Select State</option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="city" class="cityname form-control select2" id="cityname">
                    <option value="">Select City</option>
                </select>

            </div>
            <div class="col-md-2">
                <button id="search" class="btn btn-primary">Search</button>
            </div>
        </div>
        <table class="table" id="client_table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Mobile Number</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>State</th>
                    <th>City</th>
                    <th>Address</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
@endsection
@section('script')
    <script>
        $(function() {
            $('.states').empty();
            $("#stateId").select2();
            $("#cityname").select2();
            state();

            function setDefaultDates(start, end) {
                $("#start_date").val(start.format('YYYY-MM-DD'));
                $("#end_date").val(end.format('YYYY-MM-DD'));
                $('#reportrange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
            }

            $('#reportrange').daterangepicker({
                opens: 'left',
                autoUpdateInput: false,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            }, setDefaultDates);



            clientdatatable();
            $("#search").click(function() {
                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();
                var stateId = $('#stateId').val();
                var cityId = '';
                if (start_date != '' && end_date != '') {
                    $('#client_table').DataTable().destroy();
                    clientdatatable(start_date, end_date,stateId,cityId);
                } else {
                    $('#client_table').DataTable().destroy();
                    clientdatatable(start_date, end_date,stateId,cityId);
                }
            });
        });

        $("#client_table").on('click', '.copy', function() {
            navigator.clipboard.writeText($("#copytext").text());
            alert('Address Copied');
        });

        function clientdatatable(start_date = '', end_date = '',stateId='',cityId='') {
            var table = $('#client_table').DataTable({
                processing: true,
                serverSide: true,
                stateSave: true,
                "pagingType": "input",
                type: "GET",
                ajax: {
                    url: "{{ route('clients.index') }}",
                    "data": function(data) {
                        data.start_date = start_date,
                        data.end_date = end_date,
                        data.stateId = stateId,
                        data.city = 1
                        // start_date: start_date,
                        // end_date: end_date
                    }
                },
                columns: [{
                        "data": "name",
                        "searchable": true
                    },
                    {
                        "data": "mobile_number",
                        "searchable": true
                    },
                    {
                        "data": "email",
                        "searchable": false
                    },
                    {
                        "data": "gender",
                        "searchable": false
                    },
                    {
                        "data": "state",
                        "searchable": false
                    },
                    {
                        "data": "city",
                        "searchable": false
                    },
                    {
                        "data": "address",
                        "searchable": false
                    },
                    {
                        'data': "created_at",
                        "searchable": false
                    },
                    {
                        "data": "action",
                        "searchable": false
                    }
                ],
            });
        }

        function state() {
            var rootUrl = "https://geodata.phplift.net/api/index.php";
            var url = rootUrl + '?type=getStates&countryId=' + 101;
            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                success: function(data) {
                    var str1 = null;
                    $('.states').append('<option value="">Select State</option>');
                    $.each(data['result'], function(index, value) {
                        str1 = '<option value="' + value['id'] + '-' + value['name'] + '">' + value[
                            'name'] + '</option>';
                        $('.states').append(str1);
                    });
                }
            });
        }
        $(".states").on("change", function(event) {
            event.preventDefault();
            var rootUrl = "https://geodata.phplift.net/api/index.php";
            var stateId = $("#stateId").val();
            stateId = stateId.split("-")[0];
            var url = rootUrl + '?type=getCities&countryId=' + '&stateId=' + stateId;
            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                success: function(data) {
                    jQuery("#cityname option:gt(0)").remove();
                    $("#city").val('');
                    jQuery.each(data['result'], function(key, val) {
                        var option = jQuery('<option />');
                        option.attr('value', val.name).text(val.name);
                        jQuery('#cityname').append(option);
                    });
                }
            });
        });
    </script>
@endsection
