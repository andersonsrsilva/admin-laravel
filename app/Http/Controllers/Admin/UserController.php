<?php

namespace App\Http\Controllers\Admin;

use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Access\User\EloquentUserRepository;
use Validator;

class UserController extends Controller
{   

    protected $repository;

    public function __construct()
    {
        $this->repository = new EloquentUserRepository;
    }

    public function index(Request $request)
    {
        return view('admin.users.index', ['users' => User::with('roles')->sortable(['email' => 'asc'])->paginate()]);
    }

    public function restore(Request $request)
    {
        return view('admin.users.restore', ['users' => User::onlyTrashed()->with('roles')->sortable(['email' => 'asc'])->paginate()]);
    }

    public function restoreUser($id)
    {
        $status = $this->repository->restore($id);

        if($status)
        {
            return redirect()->route('admin.users')->withFlashSuccess('User Restored Successfully!');
        }

        return redirect()->route('admin.users')->withFlashDanger('Unable to Restore User!');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(User $user)
    {
        return view('admin.users.show', ['user' => $user]);
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', ['user' => $user, 'roles' => Role::get()]);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'active' => 'sometimes|boolean',
            'confirmed' => 'sometimes|boolean',
        ]);

        $validator->sometimes('email', 'unique:users', function ($input) use ($user) {
            return strtolower($input->email) != strtolower($user->email);
        });

        $validator->sometimes('password', 'min:6|confirmed', function ($input) {
            return $input->password;
        });

        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());

        $user->name = $request->get('name');
        $user->email = $request->get('email');

        if ($request->has('password')) {
            $user->password = bcrypt($request->get('password'));
        }

        $user->active = $request->get('active', 0);
        $user->confirmed = $request->get('confirmed', 0);

        $user->save();

        //roles
        if ($request->has('roles')) {
            $user->roles()->detach();

            if ($request->get('roles')) {
                $user->roles()->attach($request->get('roles'));
            }
        }

        return redirect()->intended(route('admin.users'));
    }

    public function destroy(Request $request)
    {
        $status = $this->repository->destroy($request->id);

        if($status)
        {
            return redirect()->route('admin.users')->withFlashSuccess('User Deleted Successfully!');
        }

        return redirect()->route('admin.users')->withFlashDanger('Unable to Delete User!');
    }
}
