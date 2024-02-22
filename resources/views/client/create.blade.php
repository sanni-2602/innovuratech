@extends('default')
@section('style')
    <style>
        .valid_error {
            color: red;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <h2>Client form</h2>
        @if (isset($client) && !empty($client))
            <form action={{ route('clients.update', $client) }} method="post">
                @method('put')
            @else
                <form action={{ route('clients.store') }} method="post">
        @endif
        @csrf
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" placeholder="Enter name" name="name"
                value={{ isset($client) && !empty($client->name) ? $client->name : old('name') }}>
            @error('name')
                <span class="valid_error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="mobile">Mobile:</label>
            <input type="text" class="form-control" id="mobile_number" placeholder="Enter Mobile Number"
                name="mobile_number"
                value={{ isset($client) && !empty($client->mobile_number) ? $client->mobile_number : old('mobile_name') }}
                {{ isset($client) && !empty($client->mobile_number) ? 'disabled' : '' }}>
            @error('mobile_number')
                <span class="valid_error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="text" class="form-control" id="email" placeholder="Enter email" name="email"
                value={{ isset($client) && !empty($client->email) ? $client->email : old('email') }}>
            @error('email')
                <span class="valid_error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="gender">Gender:</label>
            Male: <input type="radio" id="gender" name="gender" value="male"
                {{ isset($client->gender) && $client->gender == 'male' ? 'checked' : '' }}
                {{ isset($client->gender) && !empty($client->gender) ? 'disabled' : '' }}>
            &nbsp;&nbsp;&nbsp;Female: <input type="radio" id="gender" name="gender" value="female"
                {{ isset($client->gender) && $client->gender == 'female' ? 'checked' : '' }}
                {{ isset($client->gender) && !empty($client->gender) ? 'disabled' : '' }}>
            &nbsp;&nbsp;&nbsp;Other: <input type="radio" id="gender" name="gender" value="other"
                {{ isset($client->gender) && $client->gender == 'other' ? 'checked' : '' }}
                {{ isset($client->gender) && !empty($client->gender) ? 'disabled' : '' }}>
            @error('gender')
                <span class="valid_error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="state">State:</label>

            <select name="state" class="states form-control select2" id="stateId"
                {{ isset($client) && !empty($client->state) ? 'disabled' : '' }}>
                <option value="">Select State</option>
            </select>
            <input type="hidden" id="stateName"
                value={{ isset($client) && !empty($client->state) ? $client->state : '' }}>
            @error('state')
                <span class="valid_error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="city">City:</label>
            <select name="city" class="cityname form-control select2" id="cityId"
                {{ isset($client) && !empty($client->city) ? 'disabled' : '' }}>
                <option value="">Select City</option>
            </select>
            <input type="hidden" id="cityName" value={{ isset($client) && !empty($client->city) ? $client->city : '' }}>
            @error('city')
                <span class="valid_error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="address">Address:</label>
            <textarea name="address" class="form-control" id="address" rows="5"
                {{ isset($client->address) && !empty($client->address) ? 'disabled' : '' }}>{{ isset($client->address) ? $client->address : '' }}</textarea>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" placeholder="Enter password" name="password">
            @error('password')
                <span class="valid_error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="cpassword">Confirm Password:</label>
            <input type="password" class="form-control" id="cpassword" placeholder="Enter password" name="cpassword">
            @error('cpassword')
                <span class="valid_error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="video_url">YouTube Video URL</label>
            <input type="text" class="form-control" id="video_url" name="video_url"
                value={{ isset($client->video_url) && !empty($client->video_url) ? $client->video_url : old('video_url') }}>
        </div>

        <div id="videoThumbnail"></div>
        <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
        <i class="fa fa-calendar"></i>&nbsp;
        <span></span> <i class="fa fa-caret-down"></i>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('.states').empty();
            $("#stateId").select2();
            $("#cityId").select2();
            var stateName = $('#stateName').val();
            var cityName = $('#cityName').val();
            if (stateName) {
                fetchstate(101, stateName, cityName);
            } else {
                state();
            }

            var url = $("#video_url").val();
            var videoId = getYouTubeVideoId(url);
            // if (videoId) {
            //     var thumbnailUrl = 'https://img.youtube.com/vi/' + videoId + '/0.jpg';
            // var thumbnailContainer = document.getElementById('videoThumbnail');
            // thumbnailContainer.innerHTML = '<img src="' + thumbnailUrl + '" alt="Thumbnail">';
            // } else {
            //     document.getElementById('videoThumbnail').innerHTML = '';
            // }
            var videoUrl = url;
            if (videoId !== '') {
                var embedCode = '<iframe width="560" height="315" src="https://www.youtube.com/embed/' + videoId +
                    '" frameborder="0" allowfullscreen></iframe>';
                document.getElementById('videoThumbnail').innerHTML = embedCode;
            } else {
                document.getElementById('videoThumbnail').innerHTML = '';
            }
        });
        document.getElementById('video_url').addEventListener('input', function() {
            var url = this.value;
            var videoId = getYouTubeVideoId(url);
            // if (videoId) {
            //     var thumbnailUrl = 'https://img.youtube.com/vi/' + videoId + '/0.jpg';
            // var thumbnailContainer = document.getElementById('videoThumbnail');
            // thumbnailContainer.innerHTML = '<img src="' + thumbnailUrl + '" alt="Thumbnail">';
            // } else {
            //     document.getElementById('videoThumbnail').innerHTML = '';
            // }
            var videoUrl = url;
            if (videoId !== '') {
                var embedCode = '<iframe width="560" height="315" src="https://www.youtube.com/embed/' + videoId +
                    '" frameborder="0" allowfullscreen></iframe>';
                document.getElementById('videoThumbnail').innerHTML = embedCode;
            } else {
                document.getElementById('videoThumbnail').innerHTML = '';
            }
        });

        function getYouTubeVideoId(url) {
            // var videoId = '';
            // if (url.match(/(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/)) {
            //     videoId = RegExp.$4;
            // }
            // return videoId;
            var videoId = '';
            var match = url.match(/[?&]v=([^&]+)/);
            if (match) {
                videoId = match[1];
            } else {
                match = url.match(/\/([a-zA-Z0-9_-]{11})/);
                if (match) {
                    videoId = match[1];
                }
            }
            return videoId;
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
                    jQuery("#cityId option:gt(0)").remove();
                    $("#city").val('');
                    jQuery.each(data['result'], function(key, val) {
                        var option = jQuery('<option />');
                        option.attr('value', val.name).text(val.name);
                        jQuery('#cityId').append(option);
                    });
                }
            });
        });



        function fetchstate(countryid = 101, stateid, cityid) {

            var rootUrl = "https://geodata.phplift.net/api/index.php";
            var url = rootUrl + '?type=getStates&countryId=' + countryid;
            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                success: function(data) {
                    var str1 = null;
                    $('.states').append('<option value="">Select State</option>');
                    var stateResult = data['result'];
                    for (var i = 0; i < stateResult.length; i++) {
                        str1 = '<option value="' + stateResult[i]['id'] + '-' + stateResult[i]['name'] + '">' +
                            stateResult[i]['name'] + '</option>';
                        $('.states').append(str1);
                        var s = stateResult[i]['name'];
                        if (s == stateid) {
                            $('.states').find("option[value='" + stateResult[i]['id'] + "-" + stateResult[i][
                                'name'
                            ] + "']").attr('selected', "selected");
                            fetchcity(stateResult[i]['id'], cityid);
                        }
                    }
                }
            });
        }



        function fetchcity(stateid, cityid) {
            var rootUrl = "https://geodata.phplift.net/api/index.php";
            var url = rootUrl + '?type=getCities&countryId=' + '&stateId=' + stateid;
            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                success: function(data) {
                    var str1 = null;
                    $('.cityname').append('<option value="">Select State</option>');
                    var cityResult = data['result'];
                    for (var i = 0; i < cityResult.length; i++) {
                        str1 = '<option value="' + cityResult[i]['id'] + '-' + cityResult[i]['name'] + '">' +
                            cityResult[i]['name'] + '</option>';
                        $('.cityname').append(str1);
                        var s = cityResult[i]['name'];
                        if (s == cityid) {
                            $('.cityname').find("option[value='" + cityResult[i]['id'] + "-" + cityResult[i][
                                'name'
                            ] + "']").attr('selected', "selected");

                        }
                    }

                }
            });
        }
    </script>
@endsection
