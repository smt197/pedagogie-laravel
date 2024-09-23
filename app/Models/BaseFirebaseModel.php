<?php
namespace App\Models;

use Kreait\Firebase\Database;

abstract class BaseFirebaseModel
{
    protected $database;
    protected $table;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function create(array $data)
    {
        $newKey = $this->database->getReference($this->table)->push()->getKey();
        $this->database->getReference("{$this->table}/{$newKey}")->set($data);
        return $this->find($newKey);
    }

    public function find(string $id)
    {
        return $this->database->getReference("{$this->table}/{$id}")->getValue();
    }

    public function update(string $id, array $data)
    {
        $this->database->getReference("{$this->table}/{$id}")->update($data);
        return $this->find($id);
    }

    public function delete(string $id)
    {
        $this->database->getReference("{$this->table}/{$id}")->remove();
    }

    public function all()
    {
        return $this->database->getReference($this->table)->getValue();
    }
}
