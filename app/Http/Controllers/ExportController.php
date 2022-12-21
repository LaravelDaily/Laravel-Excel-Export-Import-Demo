<?php

namespace App\Http\Controllers;

use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Rap2hpoutre\FastExcel\FastExcel;

class ExportController extends Controller
{
    public function array()
    {
        $handle = fopen(public_path('storage/export.csv'), 'w');

        // User::chunk(2000, function ($users) use ($handle) {
        //     foreach ($users->toArray() as $user) {
        //         fputcsv($handle, $user);
        //     }
        // });

        User::query()->lazyById(2000, 'id')
            ->each(function ($user) use ($handle) {
                fputcsv($handle, $user->toArray());
            });

        fclose($handle);

        return Storage::disk('public')->download('export.csv');
    }

    public function excel()
    {
        return Excel::download(new UsersExport, 'users.csv');
    }

    public function spatie()
    {
        $rows = [];

        // User::chunk(2000, function ($users) use (&$rows) {
        //     foreach ($users->toArray() as $user) {
        //         $rows[] = $user;
        //     }
        // });

        User::query()->lazyById(2000, 'id')
            ->each(function ($user) use (&$rows) {
                $rows[] = $user->toArray();
            });
        SimpleExcelWriter::streamDownload('users.csv')
            ->noHeaderRow()
            ->addRows($rows);
    }

    protected function usersGenerator()
    {
        // this method of chunking might be a hassle, but this is more optimal than just using cursor and yielding the result
        $users = User::query();

        $chunks_per_loop = 3000; // try changing this number according to the size of your data
        $user_count = (clone $users)->count();
        $chunks = (int) ceil(($user_count / $chunks_per_loop));

        for ($i = 0; $i < $chunks; $i++) {
            $clonedUser = (clone $users)->skip($i * $chunks_per_loop)
                ->take($chunks_per_loop)
                ->cursor();

            foreach ($clonedUser as $user) {
                yield $user;
            }
        }

        // Normal, straightforward method, for smaller data this is the simplest way to use generator, but for bigger data, this is quite slow and uses a lot of memory

        // foreach (User::cursor() as $user) {
        //     yield $user;
        // }
    }

    public function fastExcel()
    {
        $filename = 'storage/fast-excel-export.csv';

        (new FastExcel($this->usersGenerator()))->export($filename);

        return response()->download(public_path($filename));
    }
}
