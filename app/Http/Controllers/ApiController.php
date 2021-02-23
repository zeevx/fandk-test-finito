<?php

namespace App\Http\Controllers;
use App\Helper\GenerateNumberHelper;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class ApiController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        //Validate Data
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8']
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Payload Validation Failed: ' . $validator->messages()->first()
            ]);
        }

        //Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'number' => GenerateNumberHelper::generateAccount(),
            'password' => Hash::make($request->password)
        ]);

        $accessToken = $user->createToken('authToken')->accessToken;

        //Send Email to User
        event(new Registered($user));

        //Return Response
        return response()->json([
            'success' => true,
            'message' => 'User Created Successfully.',
            'data' => $user,
            'token' => $accessToken
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response(['message' => 'Invalid Credentials']);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response(['user' => auth()->user(), 'token' => $accessToken]);
    }

    public function credit(Request $request)
    {
        //Get all submitted data
        $id = Auth::user()->id;
        $amount = $request->amount;
        $description = $request->description;

        //Check if amount is invalid
        if ($amount < 1){
            //Return Response
            return response()->json([
                'success' => false,
                'message' => 'Invalid Amount.'
            ]);
        }

        //Prepare transaction Model
        $transaction = new Transaction();

        //Insert data into table
        $transaction->user_id  = $id;
        $transaction->description = $description;
        $transaction->amount = $amount;
        $transaction->type = 'Card';
        $transaction->method = 'API';

        //Save
        $transaction->save();

        //Update User Wallet
        $wallet = Auth::user()->wallet + $amount;
        DB::table('users')
            ->where('id', Auth::user()->id)
            ->update(['wallet' => $wallet]);

        //Return Response
        return response()->json([
            'success' => true,
            'message' => 'User wallet funded Successfully.',
            'data' => $transaction
        ]);
    }

    public function transfer(Request $request)
    {
        //Get all submitted data
        $from_id = Auth::user()->id;
        $to_id = $request->to_user_id;
        $amount = $request->amount;
        $description = $request->description;

        //Check if amount is invalid
        if ($amount < 1){
            //Return Response
            return response()->json([
                'success' => false,
                'message' => 'Invalid Amount.'
            ]);
        }

        //Check if user have the money
        if (Auth::user()->wallet < $amount){
            //Return Response
            return response()->json([
                'success' => false,
                'message' => 'Insufficient Funds in wallet.'
            ]);
        }

        //Prepare transaction Model
        $transaction = new Transaction();
        $transaction->user_id  = $from_id;
        $transaction->description = $description;
        $transaction->amount = $amount;
        $transaction->type = 'Transfer';
        $transaction->method = 'API';

        //Save data in table
        $transaction->save();

        //Remove from user walllet
        $wallet = Auth::user()->wallet - $amount;

        //Update User wallet
        DB::table('users')
            ->where('id', Auth::user()->id)
            ->update(['wallet' => $wallet]);

        //////

        //Prepare transaction Model
        $transaction = new Transaction();
        $transaction->user_id  = $to_id;
        $transaction->description = $description;
        $transaction->amount = $amount;
        $transaction->type = 'Deposit';
        $transaction->method = 'API';

        //Save data in table
        $transaction->save();


        //Get user receiving
        $to_user = User::find($to_id);

        //Credit the other user wallet
        $wallet = $to_user->wallet + $amount;
        DB::table('users')
            ->where('id', $to_id)
            ->update(['wallet' => $wallet]);

        //Return Response
        return response()->json([
            'success' => true,
            'message' => 'Wallet transfer Successfully.'
        ]);
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function send(Request $request)
    {
        $id1 = $request->userID1;
        $id2 = $request->userID2;
        $amount  = $request->amount;
        $user = User::find($id1);
        $user->wallet = $user->wallet - $amount;
        $user->save();

        $transaction = new Transaction();
        $transaction->user_id  = $id1;
        $transaction->description = 'Testing send from device';
        $transaction->amount = $request->amount;
        $transaction->type = 'Transfer';
        $transaction->method = 'Device';
        $transaction->save();


        $user = User::find($id2);
        $user->wallet = $user->wallet + $amount;
        $user->save();

        $transaction = new Transaction();
        $transaction->user_id  = $id2;
        $transaction->description = 'Testing recieve from device';
        $transaction->amount = $request->amount;
        $transaction->type = 'Deposit';
        $transaction->method = 'Device';


        if($transaction->save()){
            $res = "{ Status: true }";
        }
        else{
            $res = "{ Status: false }";
        }
        return response()->json($res);
    }

    public function fetch($id)
    {
       $user = User::findOrFail($id);
        return response()->json($user);
    }
}
