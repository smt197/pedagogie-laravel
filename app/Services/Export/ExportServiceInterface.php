<?php

namespace App\Services\Export;

interface ExportServiceInterface
{
    public function exportToExcel(array $users);
    public function exportToPDF(array $users);
}