<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Apartment;
use App\Models\Position;
use App\Models\Service;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $apartments = Apartment::where('user_id', auth()->user()->id)->get();

        // Carbon per aggiungere le ore
        /*  foreach ($apartments as $apartment) {
            $test = $apartment->created_at->addHours(24);
        }
        dd($test); */
        return view('admin.apartments.index', compact('apartments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $services = Service::all();
        $apartment = new Apartment();

        return view('admin.apartments.create', compact('apartment', 'services'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title_desc' => 'required|string|min:5|max:255',
            'image' => 'nullable|url',
            'room' => 'required|numeric',
            'bathroom' => 'required|numeric',
            'bed' => 'required|numeric',
            'square_meters' => 'required|numeric',
            'visible' => 'boolean',
            'street' => 'required|string|min:4|max:255',
            'city' => 'required|string|min:3|max:255',
            'province' => 'required|string|max:2',
            'postal_code' => 'required|string|max:10'
        ], [
            'required' => 'il campo :attribute è obbligatorio',
            'image.url' => 'l\'immagine non è valida',
            'title_desc.min' => 'il titolo deve avere almeno 5 caratteri'
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $apartment = Apartment::create($data);

        if (array_key_exists('services', $data)) $apartment->services()->attach($data['services']);

        $data['apartment_id'] = $apartment->id;
        $position = Position::create($data);

        return redirect()->route('admin.apartments.index')->with('message', "$apartment->title_desc creato con successo");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Apartment  $apartment
     * @return \Illuminate\Http\Response
     */
    public function show(Apartment $apartment)
    {
        /* $position  = $apartment->position(); */

        return view('admin.apartments.show', compact('apartment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Apartment  $apartment
     * @return \Illuminate\Http\Response
     */
    public function edit(Apartment $apartment)
    {
        $services = Service::all();
        $current_services = $apartment->services()->pluck('id')->toArray();

        return view('admin.apartments.edit', compact('apartment', 'services', 'current_services'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Apartment  $apartment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Apartment $apartment)
    {
        $request->validate([
            'title_desc' => 'required|string|min:5|max:255',
            'image' => 'nullable|url',
            'room' => 'required|numeric',
            'bathroom' => 'required|numeric',
            'bed' => 'required|numeric',
            'square_meters' => 'required|numeric',
            'visible' => 'boolean',
        ], [
            'required' => 'il campo :attribute è obbligatorio',
            'image.url' => 'l\'immagine non è valida',
            'title_desc.min' => 'il titolo deve avere almeno 5 caratteri'
        ]);

        $data = $request->all();
        $apartment->update($data);

        if (array_key_exists('services', $data)) $apartment->services()->sync($data['services']);
        else $apartment->services()->detach();

        return redirect()->route('admin.apartments.show', $apartment)->with('message', "Complimenti hai aggionato con successo l'appartamento");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Apartment  $apartment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Apartment $apartment)
    {
        if (count($apartment->services)) $apartment->services()->detach();
        if (count($apartment->packs)) $apartment->packs()->detach();

        $apartment->delete();

        return redirect()->route('admin.apartments.index')->with('message', "L'Appartamento: $apartment->title_desc è stato eliminato con successo");
    }
}
