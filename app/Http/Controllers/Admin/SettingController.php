<?php


namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class SettingController extends Controller
{
    public function index()
    {
        $user = DB::table('users')->where('id', Auth::user()->id)->first();

        return view('pages.admin.user.profile', [
            'user' => $user
        ]);
    }

    public function upload_profile(Request $request)
    {
        $validatedData = $request->validate([
            'profile' => 'required|image|file|max:1024',
        ]);

        $id = $request->id;
        $profileImage = $request->file('profile');

        if ($profileImage) {
            // Delete existing profile picture if it exists
            $existingProfileImage = DB::table('users')->where('id', $id)->value('profile');
            if ($existingProfileImage) {
                Storage::delete($existingProfileImage);
            }

            // Upload and store the new profile picture
            $newProfileImagePath = $profileImage->store('assets/profile-images');

            // Update the user's profile picture in the database
            DB::table('users')
                ->where('id', $id)
                ->update(['profile' => $newProfileImagePath]);
        }

        return redirect()->route('user.index')->with('success', 'Sukses! Photo Pengguna telah diperbarui');
    }

    public function delete_profile(Request $request)
    {

    }

    public function change_password()
    {
        return view('pages.admin.user.change-password');
    }

    public function update_password(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['min:5', 'max:255'],
            'new_confirm_password' => ['same:new_password'],
        ]);

        $newPassword = Hash::make($request->new_password);

        DB::table('users')
            ->where('id', Auth::user()->id)
            ->update(['password' => $newPassword]);

        return redirect()->route('change-password')->with('success', 'Sukses! Password telah diperbarui');
    }
}
