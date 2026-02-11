<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $dashboardService;

    public function __construct(\App\Services\DashboardService $dashboardService)
    {
        $this->middleware('auth');
        $this->dashboardService = $dashboardService;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (view()->exists($request->path())) {
            return view($request->path());
        }
        return abort(404);
    }

    public function root()
    {
        $user = auth()->user();
        $esAdmin = $user->hasAnyRole(['super-admin', 'Admin', 'administrador']);

        $estadisticas = $this->dashboardService->obtenerEstadisticasWidgets();
        $productosTop = $this->dashboardService->obtenerProductosMasVendidos(5);
        $metodosPago = $this->dashboardService->obtenerMetodosPago();
        $ventasRecientes = $this->dashboardService->obtenerVentasRecientes(10);
        $clientesTop = $this->dashboardService->obtenerTopClientes(5);
        $ventasSemana = $this->dashboardService->obtenerVentasUltimos7Dias();

        return view('index', compact(
            'estadisticas',
            'productosTop',
            'metodosPago',
            'ventasRecientes',
            'clientesTop',
            'ventasSemana',
            'esAdmin'
        ));
    }

    /*Language Translation*/
    public function lang($locale)
    {
        if ($locale) {
            App::setLocale($locale);
            Session::put('lang', $locale);
            Session::save();
            return redirect()->back()->with('locale', $locale);
        } else {
            return redirect()->back();
        }
    }

    public function updateProfile(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'telefono' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::find($id);
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->telefono = $request->get('telefono');

        // Actualizar password si se proporcionó
        if ($request->filled('password')) {
            $user->password = Hash::make($request->get('password'));
        }

        if ($request->file('avatar')) {
            // Eliminar avatar anterior si existe
            if ($user->avatar && file_exists(public_path('images/' . $user->avatar))) {
                @unlink(public_path('images/' . $user->avatar));
            }

            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = public_path('/images/');
            $avatar->move($avatarPath, $avatarName);
            $user->avatar =  $avatarName;
        }

        $user->update();

        if ($user) {
            return redirect()->back()->with('success', '¡Perfil actualizado con éxito!');
        } else {
            return redirect()->back()->with('error', 'Hubo un error al actualizar el perfil.');
        }
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
            return response()->json([
                'isSuccess' => false,
                'Message' => "Your Current password does not matches with the password you provided. Please try again."
            ], 200); // Status code
        } else {
            $user = User::find($id);
            $user->password = Hash::make($request->get('password'));
            $user->update();
            if ($user) {
                Session::flash('message', 'Password updated successfully!');
                Session::flash('alert-class', 'alert-success');
                return response()->json([
                    'isSuccess' => true,
                    'Message' => "Password updated successfully!"
                ], 200); // Status code here
            } else {
                Session::flash('message', 'Something went wrong!');
                Session::flash('alert-class', 'alert-danger');
                return response()->json([
                    'isSuccess' => true,
                    'Message' => "Something went wrong!"
                ], 200); // Status code here
            }
        }
    }
}
