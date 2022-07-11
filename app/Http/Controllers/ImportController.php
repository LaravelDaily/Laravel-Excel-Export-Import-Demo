<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\UsersImport;
use Spatie\SimpleExcel\SimpleExcelReader;
use Rap2hpoutre\FastExcel\FastExcel;

class ImportController extends Controller
{
    public function array(Request $request)
    {
        $users = array_map('str_getcsv', file($request->file));
        $names = [];

        foreach ($users as $user) {
            $name = (string) Str::of($user[1])->before(' ');

            if (!array_key_exists($name, $names)) {
                $names[$name] = 0;
            }

            $names[$name]++;
        }

        arsort($names);

        dump(array_slice($names, 0, 10));
    }

    public function excel(Request $request)
    {
        $users = (new UsersImport)->toArray($request->file);
        $names = [];

        foreach ($users[0] as $user) {
            $name = (string) Str::of($user[1])->before(' ');

            if (!array_key_exists($name, $names)) {
                $names[$name] = 0;
            }

            $names[$name]++;
        }

        arsort($names);

        dump(array_slice($names, 0, 10));
    }

    public function spatie(Request $request)
    {
        SimpleExcelReader::create($request->file, 'csv')
            ->noHeaderRow()
            ->getRows()
            ->map(fn ($user) => (string) Str::of($user[1])->before(' '))
            ->countBy()
            ->sortDesc()
            ->take(10)
            ->dump();
    }

    public function fastExcel(Request $request)
    {
        $filePath = $request->file('file')->path();
        $newFilePath =  $filePath . '.' . $request->file('file')->getClientOriginalExtension();
        move_uploaded_file($filePath, $newFilePath);

        $users = (new FastExcel)->withoutHeaders()->import($newFilePath);

        $names = [];

        foreach ($users as $user) {
            $name = (string) Str::of($user[1])->before(' ');

            if (!array_key_exists($name, $names)) {
                $names[$name] = 0;
            }

            $names[$name]++;
        }

        arsort($names);

        dump(array_slice($names, 0, 10));
    }
}
