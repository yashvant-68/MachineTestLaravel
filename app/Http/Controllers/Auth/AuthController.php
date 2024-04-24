<?php
  
namespace App\Http\Controllers\Auth;
  
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\{User,OtpVarification};
use Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
  
class AuthController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index()
    {
        return view('auth.login');
    }  
      
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function registration()
    {
        return view('auth.registration');
    }
      
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function postLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $check_email_verified = User::where(['email'=>$request->email, 'otp_varified'=>1])->count();
        if($check_email_verified == 0){
         return redirect()->route('login')->withSuccess('Email id not verified....');
        }
   
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended('dashboard')
                        ->withSuccess('You have Successfully loggedin');
        }
  
        return redirect("login")->withSuccess('Oppes! You have entered invalid credentials');
    }
      
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function postRegistration(Request $request)
    {  
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
           
        $data = $request->all();
        $data['otp_varified'] = null;
        $check = $this->create($data);
        if($check){
            $subject = "Register OTP";
            $otp = mt_rand(100000, 999999);
            $content = "Your OTP - ".$otp;

           if(Mail::to($request->email)->send(new SendMail($subject, $content))){
              $otp_data = new OtpVarification();
              $otp_data->otp = $otp;
              $otp_data->email = $request->email;
              $otp_data->save();
           }
        }
        return view('otp');
       
        // return redirect("dashboard")->withSuccess('Great! You have Successfully loggedin');
    }
    
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function dashboard()
    {
        if(Auth::check()){
            return view('dashboard');
        }
  
        return redirect("login")->withSuccess('Opps! You do not have access');
    }
    
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function create(array $data)
    {
      return User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password'])
      ]);
    }
    
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function logout() {
        Session::flush();
        Auth::logout();
  
        return Redirect('login');
    }

    public function otp_verification(Request $request) {
        if ($request->otp) {
            $check_otp = OtpVarification::where('otp', $request->otp)->first();
            if ($check_otp) {
                $check_otp->delete();
                $exist_data = User::where('email', $check_otp->email)->first();
                if ($exist_data) {
                    $exist_data->update(['otp_varified' => 1]); // Corrected update method usage
                }
                return response()->json(['status' => 'success', 'message' => 'Your OTP verification was successful!'], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Invalid OTP'], 400);
            }
        }
    }
    
}