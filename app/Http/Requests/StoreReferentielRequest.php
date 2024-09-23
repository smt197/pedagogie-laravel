<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReferentielRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'libelle' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'required|string',
            'statut' => 'string',
            'competences' => 'required|array',
            'competences.*.nom' => 'required|string|max:255',
            'competences.*.description' => 'required|string',
            'competences.*.duree_acquisition' => 'required|string|min:1',
            'competences.*.type' => 'required|in:Back-end,Front-End',
            'competences.*.modules' => 'required|array',
            'competences.*.modules.*.nom' => 'required|string|max:255',
            'competences.*.modules.*.description' => 'required|string',
            'competences.*.modules.*.duree_acquisition' => 'required|string|min:1',
        ];
        
    }
}
