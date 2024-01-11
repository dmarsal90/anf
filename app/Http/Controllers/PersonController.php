<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PersonController extends Controller
{
    public function index()
    {
        $users = User::all();

        foreach ($users as $user) {

            $response = Http::get('https://api.nationalize.io', [
                'name' => $user->name
            ]);
            $country = collect($response->json()['country'])
                ->sortByDesc('probability')
                ->first();

            $user->nationality = $country['country_id'];
        }

        return view('users', compact('users'));

    }

    public function show(Person $person)
    {
        return response()->json([
            'persona' => $person
        ], 200);
    }
    public function store(Request $request)
    {
        $user = User::create($request->only(['nombre', 'apellidos', 'edad', 'sexo', 'correo']));

        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $this->validate($request, [
            'nombre' => 'required',
            'apellidos' => 'required',
            'edad' => 'required',
            'sexo' => 'required|max:1',
            'email' => 'required|email',
        ]);

        $user->nombre = $data['name'];
        $user->apellidos = $data['apellidos'];
        $user->edad = $data['edad'];
        $user->sexo = $data['sexo'];
        $user->correo = $data['correo'];

        $user->save();

        return response()->json(['message' => 'Usuario actualizado']);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Usuario eliminado']);
    }

}
