<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    /**
     * System table with migration history, we keep it read-only/out of CRUD.
     *
     * @var array<int, string>
     */
    protected array $blockedTables = [
        'migrations',
        'failed_jobs',
        'password_reset_tokens',
        'personal_access_tokens',
    ];

    public function index(): Response
    {
        $tables = $this->getManagedTables();

        $stats = collect($tables)->map(function (string $table) {
            return [
                'table' => $table,
                'count' => DB::table($table)->count(),
            ];
        })->values()->all();

        $totalScans = DB::table('qr_scans')->count();
        $scansToday = DB::table('qr_scans')
            ->whereDate('created_at', now()->toDateString())
            ->count();
        $dynamicQrCount = DB::table('qr_codes')->where('is_dynamic', 1)->count();
        $totalQrCount = DB::table('qr_codes')->count();
        $dynamicShare = $totalQrCount > 0
            ? (int) round(($dynamicQrCount / $totalQrCount) * 100)
            : 0;

        $topCountries = DB::table('qr_scans')
            ->selectRaw('country, COUNT(*) as scans')
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->groupBy('country')
            ->orderByDesc('scans')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'country' => (string) $row->country,
                'scans' => (int) $row->scans,
            ])
            ->values()
            ->all();

        $recentScans = DB::table('qr_scans')
            ->leftJoin('qr_codes', 'qr_codes.id', '=', 'qr_scans.qr_code_id')
            ->select([
                'qr_scans.created_at',
                'qr_scans.country',
                'qr_scans.city',
                'qr_scans.browser',
                'qr_scans.device',
                'qr_codes.content',
            ])
            ->orderByDesc('qr_scans.created_at')
            ->limit(8)
            ->get()
            ->map(fn ($row) => [
                'created_at' => (string) $row->created_at,
                'country' => (string) ($row->country ?? ''),
                'city' => (string) ($row->city ?? ''),
                'browser' => (string) ($row->browser ?? ''),
                'device' => (string) ($row->device ?? ''),
                'content' => (string) ($row->content ?? ''),
            ])
            ->values()
            ->all();

        return Inertia::render('Admin/Index', [
            'tables' => $tables,
            'stats' => $stats,
            'kpis' => [
                'totalScans' => $totalScans,
                'scansToday' => $scansToday,
                'dynamicShare' => $dynamicShare,
                'usersToday' => DB::table('users')
                    ->whereDate('created_at', now()->toDateString())
                    ->count(),
            ],
            'topCountries' => $topCountries,
            'recentScans' => $recentScans,
        ]);
    }

    public function table(Request $request, string $table): Response
    {
        $this->assertTableAllowed($table);

        $columns = $this->getColumnMeta($table);
        $primaryKey = $this->getPrimaryKey($table);

        $query = DB::table($table);
        if ($primaryKey !== null) {
            $query->orderByDesc($primaryKey);
        }

        $rows = $query->paginate(30)->withQueryString();

        return Inertia::render('Admin/TableIndex', [
            'table' => $table,
            'columns' => $columns,
            'rows' => $rows,
            'primaryKey' => $primaryKey,
            'editableColumns' => $this->getEditableColumns($columns),
            'tables' => $this->getManagedTables(),
        ]);
    }

    public function create(string $table): Response
    {
        $this->assertTableAllowed($table);

        $columns = $this->getColumnMeta($table);

        return Inertia::render('Admin/TableForm', [
            'mode' => 'create',
            'table' => $table,
            'columns' => $this->getEditableColumns($columns),
            'row' => null,
            'primaryKey' => $this->getPrimaryKey($table),
            'tables' => $this->getManagedTables(),
        ]);
    }

    public function store(Request $request, string $table): RedirectResponse
    {
        $this->assertTableAllowed($table);

        $columns = $this->getColumnMeta($table);
        $payload = $this->collectPayload($request, $columns, true);

        DB::table($table)->insert($payload);

        return redirect()->route('admin.table', ['table' => $table])->with('success', 'Запись создана');
    }

    public function edit(string $table, string $id): Response
    {
        $this->assertTableAllowed($table);

        $columns = $this->getColumnMeta($table);
        $primaryKey = $this->getPrimaryKey($table);

        abort_if($primaryKey === null, 400, 'Primary key not found for table');

        $row = DB::table($table)->where($primaryKey, $id)->first();
        abort_if(!$row, 404);

        return Inertia::render('Admin/TableForm', [
            'mode' => 'edit',
            'table' => $table,
            'columns' => $this->getEditableColumns($columns),
            'row' => (array) $row,
            'primaryKey' => $primaryKey,
            'rowId' => $id,
            'tables' => $this->getManagedTables(),
        ]);
    }

    public function update(Request $request, string $table, string $id): RedirectResponse
    {
        $this->assertTableAllowed($table);

        $columns = $this->getColumnMeta($table);
        $primaryKey = $this->getPrimaryKey($table);
        abort_if($primaryKey === null, 400, 'Primary key not found for table');

        $payload = $this->collectPayload($request, $columns, false);

        DB::table($table)->where($primaryKey, $id)->update($payload);

        return redirect()->route('admin.table', ['table' => $table])->with('success', 'Запись обновлена');
    }

    public function destroy(string $table, string $id): RedirectResponse
    {
        $this->assertTableAllowed($table);

        $primaryKey = $this->getPrimaryKey($table);
        abort_if($primaryKey === null, 400, 'Primary key not found for table');

        DB::table($table)->where($primaryKey, $id)->delete();

        return redirect()->route('admin.table', ['table' => $table])->with('success', 'Запись удалена');
    }

    protected function assertTableAllowed(string $table): void
    {
        $tables = Schema::getTableListing();
        abort_unless(in_array($table, $tables, true), 404);
        abort_if(in_array($table, $this->blockedTables, true), 403);
    }

    /**
     * @return array<int, string>
     */
    protected function getManagedTables(): array
    {
        return collect(Schema::getTableListing())
            ->reject(fn(string $table) => in_array($table, $this->blockedTables, true))
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getColumnMeta(string $table): array
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $columns = DB::select("PRAGMA table_info('$table')");

            return collect($columns)->map(function ($column) {
                $type = (string) ($column->type ?? 'text');

                return [
                    'name' => (string) $column->name,
                    'type' => $type,
                    'nullable' => ((int) ($column->notnull ?? 0)) === 0,
                    'default' => $column->dflt_value,
                    'primary' => ((int) ($column->pk ?? 0)) === 1,
                    'auto_increment' => false,
                    'input_type' => $this->mapInputType($type),
                ];
            })->values()->all();
        }

        $columns = DB::select("SHOW FULL COLUMNS FROM `$table`");

        return collect($columns)->map(function ($column) {
            $type = (string) ($column->Type ?? 'text');
            $extra = strtolower((string) ($column->Extra ?? ''));

            return [
                'name' => (string) $column->Field,
                'type' => $type,
                'nullable' => strtoupper((string) ($column->Null ?? 'YES')) === 'YES',
                'default' => $column->Default,
                'primary' => strtoupper((string) ($column->Key ?? '')) === 'PRI',
                'auto_increment' => str_contains($extra, 'auto_increment'),
                'input_type' => $this->mapInputType($type),
            ];
        })->values()->all();
    }

    protected function getPrimaryKey(string $table): ?string
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $columns = DB::select("PRAGMA table_info('$table')");
            foreach ($columns as $column) {
                if (((int) ($column->pk ?? 0)) === 1) {
                    return (string) $column->name;
                }
            }

            return null;
        }

        $key = DB::select("SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'");

        if (empty($key)) {
            return null;
        }

        return (string) ($key[0]->Column_name ?? null);
    }

    /**
     * @param array<int, array<string, mixed>> $columns
     * @return array<int, array<string, mixed>>
     */
    protected function getEditableColumns(array $columns): array
    {
        return array_values(array_filter($columns, function (array $column): bool {
            if (!empty($column['auto_increment'])) {
                return false;
            }

            return !in_array($column['name'], ['created_at', 'updated_at'], true);
        }));
    }

    /**
     * @param array<int, array<string, mixed>> $columns
     * @return array<string, mixed>
     */
    protected function collectPayload(Request $request, array $columns, bool $isCreate): array
    {
        $payload = [];
        $editableColumns = $this->getEditableColumns($columns);

        foreach ($editableColumns as $column) {
            $name = (string) $column['name'];
            $inputType = (string) $column['input_type'];
            $nullable = (bool) $column['nullable'];

            if ($inputType === 'checkbox') {
                $payload[$name] = $request->boolean($name);
                continue;
            }

            $value = $request->input($name);

            if ($value === '' || $value === null) {
                $payload[$name] = $nullable ? null : $value;
                continue;
            }

            if ($inputType === 'number') {
                $payload[$name] = is_numeric($value) ? $value + 0 : $value;
                continue;
            }

            $payload[$name] = $value;
        }

        $now = now()->toDateTimeString();
        $columnNames = collect($columns)->pluck('name')->all();

        if (in_array('updated_at', $columnNames, true)) {
            $payload['updated_at'] = $now;
        }

        if ($isCreate && in_array('created_at', $columnNames, true)) {
            $payload['created_at'] = $now;
        }

        return $payload;
    }

    protected function mapInputType(string $type): string
    {
        $type = strtolower($type);

        if (str_contains($type, 'tinyint(1)') || str_contains($type, 'bool')) {
            return 'checkbox';
        }

        if (str_contains($type, 'int') || str_contains($type, 'decimal') || str_contains($type, 'float') || str_contains($type, 'double')) {
            return 'number';
        }

        if (str_contains($type, 'text') || str_contains($type, 'json') || str_contains($type, 'longtext')) {
            return 'textarea';
        }

        if (str_contains($type, 'date') || str_contains($type, 'time')) {
            return 'datetime-local';
        }

        return 'text';
    }
}
