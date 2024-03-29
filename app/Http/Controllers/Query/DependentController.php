<?php

namespace App\Http\Controllers\Query;

use App\Http\Controllers\Controller;
use App\Models\Dependent;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class DependentController extends Controller
{

    public readonly Dependent $dependent;

    public function __construct()
    {
        $this->dependent = new Dependent();
    }

    public function index()
    {
        $dependent = Dependent::orderBy('created_at', 'DESC')->get();
        return view('dependents', compact('dependent'));
    }

     /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['string', 'max:255'],
            'cpf' => ['required', 'string', 'max:255', 'unique:'.Dependent::class],
            'birth_date' => ['required', 'date', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'vaccine_id' => ['required', 'int', 'max:255'],
        ]);
       
        try {
            $user = Dependent::create([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'cpf' => $request->cpf,
                'birth_date' => $request->birth_date,
                'phone' => $request->phone,
                'vaccine_id' => $request->vaccine_id,
                'employee_id' => Auth::user()->id,
                'is_active' => 1,
            ]);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] === 1062) { 
                return redirect(RouteServiceProvider::HOME)->with('message', 'CPF já cadastrado!')->with('type', 'danger');
            } else {
                throw $e; 
            }
        }
    
        return redirect(RouteServiceProvider::HOME);
    }

    public function update(Request $request, string $id)
    {
        $updated = $this->dependent->where('id', $id)->update($request->except('_token', '_method'));

        if ($updated) {
            return redirect()->back()->with('message', 'Dependente atualizado com sucesso!')->with('type', 'success');
        }

        return redirect()->back()->with('message', 'Erro ao atualizar dependente!')->with('type', 'danger');
    }
}
