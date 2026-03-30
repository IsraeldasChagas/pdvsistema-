<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Garante acesso ao novo módulo Financeiro para usuários existentes
        // (aplica apenas quando allowed_screens é uma lista, ou seja: vendedor/gerente).
        $rows = DB::table('users')
            ->whereIn('role', ['gerente', 'vendedor'])
            ->whereNotNull('allowed_screens')
            ->get(['id', 'allowed_screens']);

        foreach ($rows as $row) {
            $arr = json_decode((string) $row->allowed_screens, true);
            if (! is_array($arr)) {
                $arr = [];
            }
            if (! in_array('financeiro', $arr, true)) {
                $arr[] = 'financeiro';
                DB::table('users')->where('id', $row->id)->update([
                    'allowed_screens' => json_encode(array_values($arr)),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        $rows = DB::table('users')
            ->whereIn('role', ['gerente', 'vendedor'])
            ->whereNotNull('allowed_screens')
            ->get(['id', 'allowed_screens']);

        foreach ($rows as $row) {
            $arr = json_decode((string) $row->allowed_screens, true);
            if (! is_array($arr)) {
                continue;
            }
            $arr = array_values(array_filter($arr, static fn ($v) => $v !== 'financeiro'));
            DB::table('users')->where('id', $row->id)->update([
                'allowed_screens' => json_encode($arr),
                'updated_at' => now(),
            ]);
        }
    }
};
