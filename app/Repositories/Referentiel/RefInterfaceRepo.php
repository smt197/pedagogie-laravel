<?php

namespace App\Repositories\Referentiel;

use Illuminate\Http\Request;

interface RefInterfaceRepo
{
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function find();
    public function finds($id = null);
    public function findArchived();
    public function filterByCompetences($id);
    public function filterByModules($id);
    public function addCompetence($id, array $data);
    public function updateGeneralInfo($id, array $data);
    public function deleteCompetence($id, $competenceId);
    public function deleteModule($id, $competenceId, array $data);

}
