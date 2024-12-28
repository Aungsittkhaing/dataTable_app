<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        //using yajra datatables
        if ($request->ajax()) {
            $users = User::query();;
            return DataTables::eloquent($users)

                //adding index column
                ->addIndexColumn()
                //parsing date format for created_at
                ->addColumn('created_at', function ($user) {
                    return Carbon::parse($user->created_at)->format('d-m-Y');
                })
                //adding action column
                ->addColumn('action', function ($user) {
                    return '<button data-id="' . $user->id . '" class="btn btn-dark btn-sm edit-user">
                    <i class="bi bi-pencil-square"></i>
                    </button>
                    <button data-id="' . $user->id . '" class="btn btn-dark btn-sm delete-user">
                    <i class="bi bi-trash2"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('users.user');
        // $users = User::all();
        // return view('users.user', compact('users'));
    }
    public function edit($id)
    {
        $user = User::findOrFail($id);
        dd($user);
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user) {
            $user->delete();
            return response()->json(['status' => 'success', 'message' => 'User deleted successfully']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Enable to delete user']);
        }
    }
    public function update(Request $request)
    {
        try {
            $user = User::findOrFail($request->id);
            if ($user) {
                $user->name = $request->name;
                $user->email = $request->email;
                $user->phone_number = $request->phone_number;
                $user->save();
                return response()->json(['status' => 'success', 'message' => 'User updated successfully']);
            } else {
                return response()->json(['status' => 'failed', 'message' => 'Enable to update user']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
}
