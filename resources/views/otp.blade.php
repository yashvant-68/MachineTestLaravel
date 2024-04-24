@extends('layout')
  
@section('content')
<main class="login-form">
  <div class="cotainer">
      <div class="row justify-content-center">
          <div class="col-md-8">
              <div class="card">
                  <div class="card-header">Otp Varification</div>
                  <div class="card-body">
  
                      <form>
                          <div class="form-group row">
                              <label for="email_address" class="col-md-4 col-form-label text-md-right">OTP</label>
                              <div class="col-md-6">
                                  <input type="number" id="otp" value="{{old('otp')}}" class="form-control" name="otp" required autofocus>
                                  
                                      <span class="text-danger" id="errorMessage"></span>
                                  
                              </div>
                          </div>
  
                          <div class="col-md-6 offset-md-4">
                              <button type="button" id="submit" class="btn btn-primary">
                                  submit
                              </button>
                          </div>
                      </form>
                        
                  </div>
              </div>
          </div>
      </div>
  </div>
</main>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
{{-- <script>
    $(document).ready(function() {
        $('#submit').click(function() {
            let otp = $('#otp').val();
            alert(8989);
            if(otp){
                $.ajax({
                url: "{{ route('otpVarification') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    otp : otp,
                    // Any additional data you want to send
                },
                dataType : 'json',
                success: function(response) {
                    if(response.status == 'success'){
                        windon.location = '{{route('login')}}';
                    }
                    console.log(response.message);
                },
                error: function(xhr) {
                    console.log('Error:', xhr.responseText);
                }
            });
            }
           
        });
    });
</script> --}}

<script>
    $(document).ready(function() {
        $('#submit').click(function() {
            let otp = $('#otp').val();
            if (otp) {
                $.ajax({
                    url: "{{ route('otpVerification') }}", 
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        otp: otp,
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            alert('OTP Verified Successfully...');
                            window.location = '{{ route('login') }}'; // Redirect to login page on success
                        }
                    },
                    error: function(xhr) {
                        // Show error message if there's an AJAX error
                        $('#errorMessage').html('Invalid OTP. Please try again.');
                        console.log('Error:', xhr.responseText);
                    }
                });
            } else {
                // Show error message if OTP field is empty
                $('#errorMessage').html('OTP Field Required');
            }
        });
    });
</script>



@endsection