<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use App\Models\User;


class UserController extends Controller {

    public function __construct() {

    }


    public function index() {

        $users = DB::table('users')->get();

        return view('app.user.index', [
            'users' => $users
        ]);
    }


    public function addUser(Request $request) {

        $data = $request->input('params');

        $user = new User();

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->role = $data['role'];

        $user->save();
        $id = $user->id;

        $user->syncRoles([$data['role']]);

        return json_encode($id);
    }


    public function editUser(Request $request) {

        $data = $request->input('params');

        $user = User::find($data['id']);

        $user->name = $data['name'];
        $user->role = $data['role'];

        $user->save();

        $user->syncRoles([$data['role']]);

        return json_encode('success');
    }


    public function deleteUser(Request $request) {

        $data = $request->input('params');

        $user = User::find($data['id']);
        $user->delete();

        return json_encode('success');
    }


    public function updateUserPassword(Request $request) {

        $data = $request->input('params');

        $user = User::find($data['id']);
        $user->password = bcrypt($data['password']);
        $user->save();

        return json_encode('success');
    }


}
