<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Menu;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class FrontController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategoris = Kategori::all();
        $menus = Menu :: paginate(3);

        return view('menu', [
            'kategoris' => $kategoris,
            'menus' => $menus
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([

            'pelanggan' => 'required',
            'alamat' => 'required',
            'telp' => 'required',
            'jeniskelamin' => 'required',
            'email' => 'required | email | unique:pelanggans',
            'password' => 'required | min:3',

        ]);

        Pelanggan::create([
            'pelanggan' => $data['pelanggan'],
            'jeniskelamin' => $data['jeniskelamin'],
            'alamat' => $data['alamat'],
            'telp' => $data['telp'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);

        return redirect('/');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kategoris = Kategori::all();
        $menus = Menu :: where('idkategori',$id)->paginate(3);

        return view('kategori', [
            'kategoris' => $kategoris,
            'menus' => $menus
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function register()
     {
        $kategoris = Kategori::all();
        return view('register', ['kategoris' => $kategoris]);
    }

    public function login()
    {
        $kategoris = Kategori::all();
        return view('login',['kategoris' => $kategoris]);
    }

    public function postlogin(Request $request)
    {
        $data = $request->validate([
            'email' =>'required',
            'password' =>'required | min:3',
        ]);
       $pelanggan = Pelanggan::where('email', $data) -> first();

        if($pelanggan){
            if(Hash::check($data['password'],$pelanggan['password'])){
                $data = [
                    'idpelanggan' => $pelanggan['idpelanggan'],
                    'email' => $pelanggan['email'],
                ];
                $request -> session() -> put('idpelanggan', $data);
                return redirect('/');
            } else{
                return back()->with('pesan','Password Salah');
            }
        } else{
            return back()->with('pesan','Email Belum Terdaftar');
        }
    }
    public function logout()
    {
        session()->flush();
        return redirect('/');
    }
}
